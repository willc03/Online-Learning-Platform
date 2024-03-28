<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson as LessonModel;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

use App\Models\LessonItem as LessonItemModel;
use Illuminate\Validation\Rule;

class LessonItem extends Controller
{
    private array $allowedItemTypes = ['question', 'text'];

    public function create(Request $request, string $id, string $lessonId)
    {
        // Exterior validation must first occur to ensure the content is in the correct format
        $validatedData = $request->validate([
            'item-type' => [ 'required', 'string', 'in:' . implode(',', $this->allowedItemTypes) ],
            'item-title' => [ 'required', 'string' ],
            'item-description' => [ 'nullable', 'string' ],
            'item-sub-type' => [ Rule::requiredIf(fn() => $request['item-type'] === 'question'), 'string', 'in:single-choice,multi-choice,fill-in-blanks,order,match,word-search,true-false' ],
            'item-answers' => [ Rule::requiredIf(fn() => $request['item-type'] === 'question'), 'json' ],
            'item-allow-answer-changes' => [ Rule::excludeIf(fn() => !array_key_exists('item-sub-type', $request->toArray()) || (array_key_exists('item-sub-type', $request->toArray()) && !in_array($request['item-sub-type'], [ 'single-choice', 'true-false' ]) )) ],
            'item-true-or-false' => [ Rule::excludeIf(fn() => !array_key_exists('item-sub-type', $request->toArray()) || (array_key_exists('item-sub-type', $request->toArray()) && $request['item-sub-type'] != 'true-false')) ],
            'item-randomise-sides' => [ Rule::excludeIf(fn() => !array_key_exists('item-sub-type', $request->toArray()) || (array_key_exists('item-sub-type', $request->toArray()) && $request['item-sub-type'] != 'match')) ],
            'direction' => [ Rule::excludeIf(fn() => !array_key_exists('item-sub-type', $request->toArray()) || (array_key_exists('item-sub-type', $request->toArray()) && $request['item-sub-type'] != 'order')), 'required', 'string', 'in:vertical,horizontal' ],
            'item-answer-slots' => [ Rule::excludeIf(fn() => !array_key_exists('item-sub-type', $request->toArray()) || (array_key_exists('item-sub-type', $request->toArray()) && $request['item-sub-type'] != 'fill-in-blanks')), 'required', 'json' ]
        ]);
        // Convert certain elements in the array
        if (array_key_exists('item-answers',$validatedData)) {
            $validatedData['item-answers'] = Json::decode($validatedData['item-answers']);
        }
        if (array_key_exists('item-answer-slots', $validatedData)) {
            $validatedData['item-answer-slots'] = Json::decode($validatedData['item-answer-slots']);
        }
        // Create component
        $lessonItem = new LessonItemModel;
        $lessonItem->item_value = [];
        // Set the title, description, and value
        $lessonItem->item_title = $validatedData['item-title'];
        if (array_key_exists('item-description', $validatedData)) {
            $lessonItem->description = $validatedData['item-description'];
        }
        // Item-specific logic
        if ($validatedData['item-type'] == 'question') {
            $lessonItem->item_type = 'QUESTION';
            switch ($validatedData['item-sub-type']) {
                case "single-choice":
                    // Construct the array of answers
                    $choices = [];
                    $correctAnswer = null;
                    foreach ( $validatedData['item-answers'] as $itemAnswer ) {
                        $choices[] = $itemAnswer['answer']; // Array[] pushes to the end of the array
                        if ($itemAnswer['isCorrect']) {
                            $correctAnswer = $itemAnswer['answer'];
                        }
                    }
                    $lessonItem->item_value = [
                        'question_type' => 'single_choice',
                        'question_choices' => $choices,
                        'correct_answer' => $correctAnswer,
                        'one_time_answer' => !array_key_exists('item-allow-answer-changes', $validatedData)
                    ];
                    break;
                case "multi-choice":
                    // Construct the array of answers
                    $choices = [];
                    $correctAnswers = [];
                    foreach ( $validatedData['item-answers'] as $itemAnswer ) {
                        $choices[] = $itemAnswer['answer']; // Array[] pushes to the end of the array
                        if ($itemAnswer['isCorrect']) {
                            $correctAnswers[] = $itemAnswer['answer'];
                        }
                    }
                    $lessonItem->item_value = [
                        'question_type' => 'multiple_choice',
                        'question_choices' => $choices,
                        'correct_answers' => $correctAnswers,
                    ];
                    break;
                case "true-false":
                    $lessonItem->item_value = [
                        'question_type' => "true_or_false",
                        'one_time_answer' => !array_key_exists('item-allow-answer-changes', $validatedData),
                        'correct_answer' => array_key_exists('item-true-or-false', $validatedData)
                    ];
                    break;
                case "match":
                    // Set the correct answers
                    $correctAnswers = [];
                    foreach ( $validatedData['item-answers'] as $itemAnswer ) {
                        $correctAnswers[] = [ $itemAnswer['match_one'], $itemAnswer['match_two'] ];
                    }
                    // Set the item value
                    $lessonItem->item_value = [
                        'question_type' => "match",
                        'items_to_match' => $correctAnswers,
                        'are_sides_random' => array_key_exists('item-randomise-sides', $validatedData)
                    ];
                    break;
                case "word-search":
                    // Set the correct answers
                    $correctAnswers = [];
                    foreach ( $validatedData['item-answers'] as $itemAnswer ) {
                        $correctAnswers[] = [ $itemAnswer['word'], $itemAnswer['message'] ];
                    }
                    // Set the item value
                    $lessonItem->item_value = [
                        'question_type' => "wordsearch",
                        'words' => $correctAnswers
                    ];
                    break;
                case "order":
                    // Get the correct answer
                    $correctAnswer = [];
                    foreach ($validatedData['item-answers'] as $answer) {
                        $correctAnswer[] = $answer['answer'];
                    }
                    // Set the item value
                    $lessonItem->item_value = [
                        'question_type' => "order",
                        'direction' => $validatedData['direction'],
                        'answer_slots' => $correctAnswer,
                        'correct_answer' => $correctAnswer
                    ];
                    break;
                case "fill-in-blanks":
                    // Set the available answers
                    $correctAnswers = [];
                    foreach ($validatedData['item-answers'] as $answerSet) {
                        $correctAnswers[] = $answerSet['answer'];
                    }
                    // Set the item value
                    $lessonItem->item_value = [
                        'question_type' => "fill_in_blanks",
                        'question_choices' => $validatedData['item-answer-slots'],
                        'correct_answers' => $correctAnswers
                    ];
                    break;
                default:
                    return back()->withErrors([ 'INVALID-SUB-TYPE' => 'The selected sub-type is invalid.' ]);
            }
        } elseif ($validatedData['item-type'] == 'text') {
            $lessonItem->item_type = 'TEXT';
        }
        $lessonItem->position = LessonItemModel::whereLessonId($lessonId)->max('position') + 1;
        $lessonItem->lesson_id = $lessonId;
        // Save component
        if ($lessonItem->save()) {
            return redirect()->to(route('course.lesson.configure.get', [ 'id' => $id, 'lessonId' => $lessonId ]));
        } else {
            return back()->withErrors([ 'LESSON_ITM' => 'Could not create a lesson item.' ]);
        }
    }

    public function delete(Request $request, string $id, string $lessonId)
    {
        // We assume the user owns the course here due to the route being protected by middleware.
        // Sanitise the request
        $validatedData = $request->validate([
            'edit-type' => [ 'required', 'string', 'in:component-delete' ],
            'data' => [ 'required', 'string', 'exists:lesson_items,id' ],
        ]);
        // Remove the component
        $lessonItem = LessonItemModel::whereId($validatedData['data'])->firstOrFail();
        if ( !$lessonItem->delete() ) {
            return response("Could not delete the record.", 500);
        }
        return response("Successfully removed the record.", 200);
    }
}
