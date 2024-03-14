<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
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
        ]);
        // Convert certain elements in the array
        if (array_key_exists('item-answers',$validatedData)) {
            $validatedData['item-answers'] = Json::decode($validatedData['item-answers']);
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
                        'one_time_answer' => array_key_exists('item-allow-answer-changes', $validatedData)
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
                        'one_time_answer' => array_key_exists('item-allow-answer-changes', $validatedData),
                        'correct_answer' => array_key_exists('item-true-or-false', $validatedData)
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

    public function modify(string $id, string $lessonId)
    {

    }

    public function delete(string $id, string $lessonId)
    {

    }
}
