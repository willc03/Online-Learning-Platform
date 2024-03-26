<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson as LessonModel;
use App\Models\LessonItem;
use App\Models\UserCompletedLesson;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Lesson extends Controller
{

    /**
     * Create a route function to allow the user to begin a lesson.
     * For security and consistency reasons, sessions will be utilised
     * in this controller to ensure the user cannot manipulate the lesson
     * on the client.
     *
     * @param string $id       The course id field.
     * @param string $lessonId The lesson id field.
     *
     * @return RedirectResponse Redirects the user to home if the lesson doesn't exist, or the lesson start page if successful.
     *
     */
    public function start ( string $id, string $lessonId )
    {
        // Check the lesson exists and can be used
        if ( !$lesson = $this->processLessonId($lessonId) ) {
            return redirect()->to(route('course.home', ['id' => $id]))->withErrors(['LESSON_DOES_NOT_EXIST' => 'The requested lesson could not be found or does not exist!']);
        }
        // If the lesson exists, begin
        session()->put('lesson', [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ]);
        return redirect()->to(route('course.lesson.main', ['id' => $id, 'lessonId' => $lessonId]));
    }

    /**
     * Processes the lessonId passed in, ensuring the lesson exists and that it can be started.
     *
     * @param string $lessonId The lesson id field.
     *
     * @return LessonModel|false Returns the lesson model if found or false if it is not.
     */
    private function processLessonId ( string $lessonId )
    {
        $lessonQuery = LessonModel::where(['id' => $lessonId]);
        if ( !$lessonQuery->exists() ) {
            return false;
        } else {
            return $lessonQuery->firstOrFail();
        }
    }

    /**
     * Create a route to display the content of the lesson to
     * the user.
     *
     * @param string $id       The course id field.
     * @param string $lessonId The lesson id field.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse Returns a view or a redirect
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display ( string $id, string $lessonId )
    {
        // Checks have already been conducted to ensure the user has access to the course,
        // so it can simply be declared here
        $course = Course::findOrFail($id);
        // Now check the lesson exists and can be used
        if ( !$lesson = $this->processLessonId($lessonId) ) {
            return redirect()->to(route('course.home', ['id' => $id]))->withErrors(['LESSON_DOES_NOT_EXIST' => 'The requested lesson could not be found or does not exist!']);
        }
        /*
         * LESSON LOGIC TO GO HERE
         */
        if ( session()->get('lesson.position', -1) === -1 ) {
            return view('lesson.start', [
                'course' => $course,
                'lesson' => $lesson,
            ]);
        } else {
            // Get the progression percentage
            $lessonItemQuery = LessonItem::where(['lesson_id' => $lessonId]);
            $lessonItems = $lessonItemQuery->orderBy('position')->get();
            $percentage = floor(( session()->get('lesson.position', 1) / count($lessonItems->toArray()) ) * 100);
            return view('lesson.question', [
                'course'     => $course,
                'lesson'     => $lesson,
                'question'   => $lessonItemQuery->wherePosition(session()->get('lesson.position', 1))->firstOrFail(),
                'percentage' => $percentage,
            ]);
        }
    }

    /**
     * Processes AJAX answer requests. The route is named partial because the answers are such.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return mixed|string|null Returns whether the provided answer is correct, or null if the request isn't valid
     */
    public function partial ( Request $request )
    {
        // Set up some required information to process the answer
        $validatedData = $request->validate([
            'question_id' => ['required'],
            'answer'      => ['required'],
        ]);

        // Get the details on the question
        $question = LessonItem::where('id', $validatedData['question_id'])->firstOrFail();

        switch ( $question->item_value['question_type'] ) {
            case "match":
                // Retrieve the question information
                $questionInfo = $question->item_value;
                // Check if the answer is correct
                foreach ( $questionInfo['items_to_match'] as $matchPair ) {
                    if ( $matchPair[0] == $validatedData['answer'][0] && $matchPair[1] == $validatedData['answer'][1] ) {
                        return 'true';
                    }
                }
                return 'false';

            case "wordsearch":
                // Get the question info
                $questionInfo = $question->item_value;
                // Check the answer is a word
                $userWord = strtolower(implode($validatedData['answer']));
                for ( $i = 0 ; $i < count($questionInfo['words']) ; $i++ ) {
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
     * @param Request $request  The HTTP request provided by Laravel
     * @param string  $id       The course id field.
     * @param string  $lessonId The lesson id field
     *
     * @return RedirectResponse All answers return redirects (to the next item or back with an error)
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function answer ( Request $request, string $id, string $lessonId )
    {
        // Create a flag for whether the answer is correct
        $isAnswerCorrect = false;
        // Ensure the necessary components are
        $validatedData = $request->validate([
            'question_id' => ['required', Rule::requiredIf(fn () => Rule::exists('lesson_items', 'id') || Rule::in('start'))],
            'answer'      => [Rule::excludeIf(fn () => $request['question_id'] === "start"), Rule::excludeIf(fn () => !LessonItem::whereId($request['question_id'])->exists() || LessonItem::findOrFail($request['question_id'])->item_type == "TEXT"), 'required'],
        ]);
        // Process the user to the first question if the id is start
        if ( $validatedData['question_id'] == 'start' ) {
            session()->put('lesson.position', 1);
            return redirect()->to(route('course.lesson.main', ['id' => $id, 'lessonId' => $lessonId]));
        }
        // Process logic with the lesson item
        $lessonItem = LessonItem::findOrFail($validatedData['question_id']);
        // Process already-answered questions
        if ( in_array($lessonItem->id, session()->get('lesson.answered', [])) ) {
            return redirect()->to(route('course.lesson.main', ['id' => $id, 'lessonId' => $lessonId]))->withErrors(['ALREADY_ANSWERED' => 'You cannot resubmit an answer to a completed question!']);
        }
        // Process new answers
        switch ( $lessonItem->item_type ) {
            case "TEXT":
                $isAnswerCorrect = true; // Always progress to the next answer
                break;
            case "QUESTION":
                $questionType = $lessonItem->item_value['question_type'];

                if ( $questionType != 'multiple_choice' && $questionType != 'fill_in_blanks' && $questionType != 'match' && $questionType != 'wordsearch' ) {
                    $correctAnswer = $lessonItem->item_value['correct_answer'];
                }

                switch ( $questionType ) {
                    case 'single_choice':
                        $isAnswerCorrect = ( $correctAnswer == $validatedData['answer'] );
                        break;
                    case 'multiple_choice':
                        $correctAnswers = array_map('strval', $lessonItem->item_value['correct_answers']);
                        $matchedAnswers = count(array_intersect($correctAnswers, Json::decode($validatedData['answer'])));
                        $isAnswerCorrect = ( $matchedAnswers == count($correctAnswers) );
                        break;
                    case 'fill_in_blanks':
                        $correctAnswers = array_map('strtolower', $lessonItem->item_value['correct_answers']);
                        $userAnswers = array_map('strtolower', Json::decode($validatedData['answer']));
                        $isAnswerCorrect = ( $correctAnswers === $userAnswers );
                        break;
                    case 'true_or_false':
                        $isAnswerCorrect = ( $correctAnswer == filter_var($validatedData['answer'], FILTER_VALIDATE_BOOLEAN) );
                        break;
                    case 'order':
                        $correctAnswers = $lessonItem->item_value['correct_answer'];
                        // Convert the answer into an array
                        $validatedData['answer'] = Json::decode($validatedData['answer']);
                        for ( $i = 0 ; $i < count($correctAnswers) ; $i++ ) {
                            $validatedData['answer'][$i] = rtrim($validatedData['answer'][$i], " \t\n\r\0\x0B");
                        }
                        // Check the answers
                        $isAnswerCorrect = true;
                        for ( $i = 0 ; $i < count($validatedData['answer']) ; $i++ ) {
                            if ( $validatedData['answer'][$i] != $correctAnswers[$i] ) {
                                $isAnswerCorrect = false;
                                break;
                            }
                        }
                        break;
                    case 'match':
                    case 'wordsearch':
                        $isAnswerCorrect = true;
                        break;
                }
                break;
            default:
                return back()->withErrors(['UNSUPPORTED_ITEM' => 'The item type is unsupported.']);
                break;
        }
        // If the item is a question, manage the streak.
        if ( $lessonItem->item_type == 'QUESTION' ) {
            if ( $isAnswerCorrect ) {
                session()->increment('lesson.xp', 100 * session()->get('lesson.streak', 1));
                session()->increment('lesson.streak', 0.1);
            } else {
                session()->put('lesson.streak', 1);
            }
        }
        // Progress to the next item or return with an error.
        if ( $isAnswerCorrect ) {
            session()->push('lesson.answered', $lessonItem->id);
            $nextPositionQuery = LessonItem::where([
                ['lesson_id', '=', $lessonId],
                ['position', '>', $lessonItem->position],
            ]);
            if ( $nextPositionQuery->count() > 0 ) { // There is another question or item
                session()->put('lesson.position', $nextPositionQuery->min('position'));
                return redirect()->to(route('course.lesson.main', ['id' => $id, 'lessonId' => $lessonId]));
            } else { // There is no more content left, the lesson is done.
                return $this->complete($request, $id, $lessonId);
            }
        } else {
            return back()->withErrors(['WRONG' => 'This answer is incorrect! Not to worry, have another go!']);
        }
    }

    /**
     * This private function will process completed lessons as part of the answer route
     *
     * @param Request $request  The HTTP request provided by Laravel.
     * @param string  $id       The course id field.
     * @param string  $lessonId The lesson id field.
     *
     * @return RedirectResponse Completed lessons return back with an error or redirect to course home with a success message.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function complete ( Request $request, string $id, string $lessonId )
    {
        // Create the database entry
        $completedLessonRecord = new UserCompletedLesson;
        $completedLessonRecord->lesson_id = $lessonId;
        $completedLessonRecord->user_id = $request->user()->id;
        $completedLessonRecord->score = session()->get('lesson.xp', -1);
        // Attempt to save the record
        if ( $completedLessonRecord->save() ) {
            $score = session()->get('lesson.xp', 0);
            session()->forget('lesson');
            return redirect()->to(route('course.home', ['id' => $id]))->with(['COMPLETED_LESSON' => $score, 'LESSON_TITLE' => LessonModel::whereId($lessonId)->firstOrFail()->title]);
        } else {
            return back()->withErrors(['RECORD_SAVE_ERROR' => 'Could not upload the lesson completion record to the database. Please try again.']);
        }
    }

    /**
     * Return the view with information to allow the user to configure a lesson.
     *
     * @param string $id       The course id field.
     * @param string $lessonId The lesson id field.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function config ( string $id, string $lessonId )
    {
        return view('lesson.config', [
            'course' => Course::whereId($id)->firstOrFail(),
            'lesson' => LessonModel::whereId($lessonId)->firstOrFail(),
        ]);
    }

    /**
     * Delivers forms to the user using AJAX requests.
     *
     * @param Request $request  The HTTP request provided by Laravel.
     * @param string  $id       The course id field.
     * @param string  $lessonId The lesson id field.
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|int|View Returns a view containing the form (or 400 error).
     */
    public function formRequest ( Request $request, string $id, string $lessonId )
    {
        // Validation
        $validatedData = $request->validate([
            'form-name' => ['required', 'string', 'in:text,question,single-choice,multi-choice,fill-in-blanks,order,match,word-search,true-false',],
            'form-type' => ['nullable', 'string'],
        ]);
        // Get the course
        $course = Course::find($id);
        // Get the form
        if ( !( array_key_exists('form-type', $validatedData) && $validatedData['form-type'] == 'question' ) ) {
            return match ( $validatedData['form-name'] ) {
                'question' => view('components.courses.lessons.component_forms.question', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'text'     => view('components.courses.lessons.component_forms.text', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                default    => 400,
            };
        } else {
            return match ( $validatedData['form-name'] ) {
                'single-choice'  => view('components.courses.lessons.question_forms.single_choice', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'multi-choice'   => view('components.courses.lessons.question_forms.multi_choice', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'fill-in-blanks' => view('components.courses.lessons.question_forms.fill_blanks', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'true-false'     => view('components.courses.lessons.question_forms.true_false', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'order'          => view('components.courses.lessons.question_forms.order', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'match'          => view('components.courses.lessons.question_forms.match', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                'word-search'    => view('components.courses.lessons.question_forms.word_search', ['course' => $course, 'lesson' => LessonModel::find($lessonId)]),
                default          => 400,
            };
        }
    }

    /**
     * This public route will be used to display completed lesson attempts to the owner of a course
     *
     * @param string $id       The course id field.
     * @param string $lessonId The lesson id field.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View Returns a view containing the attempts (if any)
     */
    public function attempts ( string $id, string $lessonId )
    {
        // Calculate maximum score
        $total = 0;
        $multiplier = 1;
        $n = LessonItem::where(['lesson_id' => $lessonId, 'item_type' => 'QUESTION'])->count();
        for ( $i = 0 ; $i < $n ; $i++ ) {
            $total += ( 100 * $multiplier );
            $multiplier += 0.1;
        }
        // Get attempts
        $attempts = UserCompletedLesson::whereLessonId($lessonId)->get();
        // Display the view
        return view('lesson.attempt', ['attempts' => $attempts, 'course' => Course::findOrFail($id), 'maxScore' => $total]);
    }

    /**
     * Allows the user to end their current lesson (a button is provided to facilitate this
     * on the lesson pages.
     *
     * @param string $id       The course id field.
     * @param string $lessonId The lesson id field.
     *
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function end ( string $id, string $lessonId )
    {
        if ( session()->get('lesson.id', null) == $lessonId ) {
            session()->pull('lesson');
            return redirect()->to(route('course.home', ['id' => $id]));
        }
        return back()->withErrors(['NO_LESSON' => 'Could not end the lesson, please try again.']);
    }

}
