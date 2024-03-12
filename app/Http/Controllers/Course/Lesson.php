<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson as LessonModel;
use App\Models\LessonItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Illuminate\Database\Eloquent\Casts\Json;

class Lesson extends Controller
{
    /**
     * Create a private function that will be used to process the lessonId passed in.
     * This will ensure the lesson exists and that it can be started.
     *
     * @return LessonModel|false
     */
    private function processLessonId ( string $lessonId )
    {
        $lessonQuery = LessonModel::where([ 'id' => $lessonId ]);
        if ( !$lessonQuery->exists() ) {
            return false;
        } else {
            return $lessonQuery->firstOrFail();
        }
    }

    /**
     * Create a route function to allow the user to begin a lesson.
     * For security and consistency reasons, sessions will be utilised
     * in this controller to ensure the user cannot manipulate the lesson
     * on the client.
     *
     * @param string $id
     * @param string $lessonId
     *
     * @return RedirectResponse
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function start ( string $id, string $lessonId )
    {
        // Check the lesson exists and can be used
        if ( !$lesson = $this->processLessonId($lessonId) ) {
            return redirect()->to(route('course.home', [ 'id' => $id ]))->withErrors([ 'LESSON_DOES_NOT_EXIST' => 'The requested lesson could not be found or does not exist!' ]);
        }
        // If the lesson exists, begin
        if ( session()->has('lesson') && session()->get('lesson.id', null) !== $lesson->id ) {
            return redirect()->to(route('course.lesson.main', [ 'id' => $id, 'lessonId' => session()->get('lesson.id') ]))->withErrors([ 'ALREADY_IN_LESSON' => 'You cannot start another lesson when one is in progress!' ]);
        } elseif ( session()->has('lesson') && session()->get('lesson.id', null) === $lesson->id ) {
            return redirect()->to(route('course.lesson.main', [ 'id' => $id, 'lessonId' => $lessonId ]));
        } else {
            session()->put('lesson', [
                'id' => $lesson->id,
                'position' => -1,
                'streak' => 0,
                'xp' => 0
            ]);
            return redirect()->to(route('course.lesson.main', [ 'id' => $id, 'lessonId' => $lessonId ]));
        }
    }

    /**
     * Create a route to display the content of the lesson to
     * the user.
     *
     * @param $id
     * @param $lessonId
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display ( $id, $lessonId )
    {
        // Checks have already been conducted to ensure the user has access to the course,
        // so it can simply be declared here
        $course = Course::findOrFail($id);
        // Now check the lesson exists and can be used
        if ( !$lesson = $this->processLessonId($lessonId) ) {
            return redirect()->to(route('course.home', [ 'id' => $id ]))->withErrors([ 'LESSON_DOES_NOT_EXIST' => 'The requested lesson could not be found or does not exist!' ]);
        }
        // If the lesson exists, do a quick check to ensure it is the correct lesson
        if ( session()->has('lesson') && session()->get('lesson.id', null) !== $lesson->id ) {
            return redirect()->to(route('course.lesson.main', [ 'id' => $id, 'lessonId' => $lessonId ]))->withErrors([ 'ALREADY_IN_LESSON' => 'You cannot start another lesson when one is in progress!' ]);
        }
        /*
         * LESSON LOGIC TO GO HERE
         */
        if ( session()->get('lesson.position', null) === -1 ) {
            return view('lesson.start', [
                'course' => $course,
                'lesson' => $lesson
            ]);
        } else {
            $question = LessonItem::where([ 'position' => session()->get('lesson.position') ]);
            return view('lesson.question', [
                'course' => $course,
                'lesson' => $lesson,
                'question' => $question->firstOrFail()
            ]);
        }
    }

    public function partial ( Request $request )
    {
        // Set up some required information to process the answer
        $validatedData = $request->validate([
            'question_id' => [ 'required' ],
            'answer' => [ 'required' ]
        ]);

        // Get the details on the question
        $question = LessonItem::where('id', $validatedData['question_id'])->firstOrFail();

        switch ( $question->item_value['question_type'] ) {
            case "match":
                // Retrieve the question information
                $questionInfo = $question->item_value;
                // Check if the answer is correct
                for ( $i = 0; $i < count($questionInfo['items_to_match']); $i++ ) {
                    if ( count(array_intersect($questionInfo["items_to_match"][$i], $validatedData['answer'])) == count($validatedData['answer']) ) {
                        return 'true';
                    }
                }
                return 'false';

            case "wordsearch":
                // Get the question info
                $questionInfo = $question->item_value;
                // Check the answer is a word
                $userWord = strtolower(implode($validatedData['answer']));
                for ( $i = 0; $i < count($questionInfo['words']); $i++ ) {
                    if ( $userWord == strtolower($questionInfo["words"][$i][0]) || strrev($userWord) == strtolower($questionInfo["words"][$i][0]) ) {
                        return $questionInfo['words'][$i];
                    }
                }
                return 'false';
        }

        return null;
    }

    /**
     * This public function will be used to process submitted answers and direct the user to
     * either the next question or the current one (if the answer is incorrect).
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function answer ( Request $request, $id, $lessonId )
    {
        if (session()->get('lesson.position') === -1) { // Go to the first question if they click begin
            session()->put('lesson.position', 1);
            return redirect()->to(route('course.lesson.main', [ 'id' => $id, 'lessonId' => $lessonId ]));
        }

        $validatedData = $request->validate([
            'question_id' => 'required',
            'answer' => 'required',
        ]);

        $question = LessonItem::findOrFail($validatedData['question_id']);
        $questionType = $question->item_value['question_type'];

        if ($questionType != 'multiple_choice' && $questionType != 'fill_in_blanks' && $questionType != 'match' && $questionType != 'wordsearch') {
            $correctAnswer = $question->item_value['correct_answer'];
        }

        $isAnswerCorrect = false;

        switch ( $questionType ) {
            case 'single_choice':
                $isAnswerCorrect = ( $correctAnswer == $validatedData['answer'] );
                break;
            case 'multiple_choice':
                $correctAnswers = array_map('strval', $question->item_value['correct_answers']);
                $matchedAnswers = count(array_intersect($correctAnswers, Json::decode($validatedData['answer'])));
                $isAnswerCorrect = ($matchedAnswers == count($correctAnswers));
                break;
            case 'fill_in_blanks':
                $correctAnswers = array_map('strtolower', $question->item_value['correct_answers']);
                $userAnswers = array_map('strtolower', Json::decode($validatedData['answer']));
                $isAnswerCorrect = ( $correctAnswers === $userAnswers );
                break;
            case 'true_or_false':
                $isAnswerCorrect = ( $correctAnswer == filter_var($validatedData['answer'], FILTER_VALIDATE_BOOLEAN) );
                break;
            case 'order':
                $correctAnswers = array_map('strtolower', $correctAnswer);
                $userAnswers = array_map('strtolower', Json::decode($validatedData['answer']));
                $isAnswerCorrect = ( $correctAnswers === $userAnswers );
                break;
            case 'match':
            case 'wordsearch':
                $isAnswerCorrect = true;
                break;
        }

        if ( $isAnswerCorrect ) {
            $nextPositionRequirements = [
                ['lesson_id', '=', $lessonId],
                ['position', '>', session()->get('lesson.position', -1)]
            ];
            $nextPositionQuery = LessonItem::where($nextPositionRequirements);
            if ($nextPositionQuery->count() > 0) { // There is another question or item
                session()->put('lesson.position', $nextPositionQuery->min('position'));
                return redirect()->to(route('course.lesson.main', [ 'id' => $id, 'lessonId' => $lessonId ]));
            } else { // There is no more content left, the lesson is done.
                return redirect()->to(url('lessondone'));
            }
        } else {
            return back()->withErrors(['WRONG' => 'This answer is incorrect! Not to worry, have another go!']);
        }
    }

    public function config($id, $lessonId) {
        return view('lesson.config', [
            'course' => Course::whereId($id)->firstOrFail(),
            'lesson' => LessonModel::whereId($lessonId)->firstOrFail()
        ]);
    }

    /**
     * This function will be used to deliver forms to the user through AJAX requests
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|int|View
     */
    public function formRequest(Request $request, $id, $lessonId) {
        // Validation
        $validatedData = $request->validate([
            'form-name' => ['required', 'string', 'in:text,question,single-choice,multi-choice,fill-in-blanks,order,match,word-search,true-false',],
            'form-type' => [ 'nullable', 'string' ]
        ]);
        // Get the course
        $course = Course::find($id);
        // Get the form
        if (!(array_key_exists('form-type', $validatedData) && $validatedData['form-type'] == 'question')) {
            return match ($validatedData['form-name']) {
                'question' => view('components.courses.lessons.component_forms.question', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'text' => view('components.courses.lessons.component_forms.text', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                default => 400,
            };
        } else {
            return match ($validatedData['form-name']) {
                'single-choice' => view('components.courses.lessons.question_forms.single_choice', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'multi-choice' => view('components.courses.lessons.question_forms.multi_choice', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'fill-in-blanks' => view('components.courses.lessons.question_forms.fill_blanks', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'true-false' => view('components.courses.lessons.question_forms.true_false', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'order' => view('components.courses.lessons.question_forms.order', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'match' => view('components.courses.lessons.question_forms.match', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'word-search' => view('components.courses.lessons.question_forms.word_search', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                default => 400,
            };
        }
    }
}
