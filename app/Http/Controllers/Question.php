<?php

namespace App\Http\Controllers;

use App\Models\LessonItem;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

class Question extends Controller
{
    /**
     * This function will show either the question specified or the home page
     * if the given question doesn't exist by ID.
     *
     * @param string $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|string
     */
    public function index(string $id)
    {
        if ($id) {
            $question = LessonItem::where('id', $id)->firstOrFail();
            return view('question', $question->toArray());
        } else {
            return route('home');
        }
    }

    /**
     * This route will be used to process partial answers submitted through
     * AJAX requests.
     *
     * @param Request $request
     * @return mixed|string|null
     */
    public function partial(Request $request)
    {
        // Set up some required information to process the answer
        $validatedData = $request->validate([
            'question_id' => ['required'],
            'answer' => ['required']
        ]);

        // Get the details on the question
        $question = LessonItem::where('id', $validatedData['question_id'])->firstOrFail();

        switch ($question->item_value['question_type']) {
            case "match":
                // Retrieve the question information
                $questionInfo = $question->item_value;
                // Check if the answer is correct
                for ($i = 0; $i < count($questionInfo['items_to_match']); $i++) {
                    if (count( array_intersect($questionInfo["items_to_match"][$i], $validatedData['answer']) ) == count($validatedData['answer'])) {
                        return 'true';
                    }
                }
                return 'false';
                break;

            case "wordsearch":
                // Get the question info
                $questionInfo = $question->item_value;
                // Check the answer is a word
                $userWord = strtolower(implode($validatedData['answer']));
                for ($i = 0; $i < count($questionInfo['words']); $i++) {
                    if ( $userWord == strtolower($questionInfo["words"][$i][0]) || strrev($userWord) == strtolower($questionInfo["words"][$i][0]) ) {
                        return $questionInfo['words'][$i];
                    }
                }
                return 'false';
                break;
        }

        return null;
    }

    /**
     * This public function will be used to process submitted answers and direct the user to
     * either the next question or the current one (if the answer is incorrect).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function answer(Request $request)
    {
        // Set up some required information to process the answer
        $validatedData = $request->validate([
            'question_id' => ['required'],
            'answer' => ['required']
        ]);

        // Get the details on the question
        $question = LessonItem::where('id', $validatedData['question_id'])->firstOrFail();
        $question = $this->formatQuestion($question);

        // Variables for redirection
        $isAnswerCorrect = false;

        switch ($question->item_value['question_type']) {
            case 'single_choice':
                $correctAnswer = $question->item_value['correct_answer'];
                $isAnswerCorrect = ($correctAnswer == $validatedData['answer']);
                break;
            case 'multiple_choice':
                $correctAnswers = $question->item_value['correct_answers'];
                $answersToMatch = count($correctAnswers);
                $matchedAnswers = 0;
                $validatedData['answer'] = Json::decode($validatedData['answer']);
                foreach($validatedData['answer'] as $answer) {
                    if (in_array($answer, $validatedData['answer'])) {
                        $matchedAnswers++;
                    }
                }
                $isAnswerCorrect = ($matchedAnswers == $answersToMatch);
                break;
            case 'fill_in_blanks':
                $correctAnswers = $question->item_value['correct_answers'];
                $isOrderCorrect = true;
                $validatedData['answer'] = Json::decode($validatedData['answer']);
                for ($i = 0; $i < count($correctAnswers); $i++) {
                    if (strtolower($correctAnswers[$i]) != strtolower($validatedData['answer'][$i])) {
                        $isOrderCorrect = false;
                    }
                }
                $isAnswerCorrect = $isOrderCorrect;
                break;
            case 'true_or_false':
                $correctAnswer = $question->item_value['correct_answer'];
                $isAnswerCorrect = ($correctAnswer == filter_var($validatedData['answer'], FILTER_VALIDATE_BOOLEAN));
                break;
            case 'order':
                $correctAnswer = $question->item_value['correct_answer'];
                $isOrderCorrect = true;
                $validatedData['answer'] = Json::decode($validatedData['answer']);
                // Sanitise the validated answer further
                for ($i = 0; $i < count($correctAnswer); $i++) {
                    $correctAnswer[$i] = ltrim($correctAnswer[$i]);

                    $temp = $validatedData['answer'][$i];
                    $temp = str_replace("\n","", $temp);
                    $temp = ltrim($temp);
                    $validatedData['answer'][$i] = $temp;
                }
                // Loop to check the order
                for ($i = 0; $i < count($correctAnswer); $i++) {
                    if (strtolower($correctAnswer[$i]) != strtolower($validatedData['answer'][$i])) {
                        $isOrderCorrect = false;
                    }
                }
                $isAnswerCorrect = $isOrderCorrect;
                break;
            case 'match':
            case 'wordsearch':
                $isAnswerCorrect = true; // These questions are all handled on the client aside from AJAX requests, this is simply
                                         // to redirect the user to the next question
                break;
        }

        if ($isAnswerCorrect) {
            return redirect()->to(url("correct")); // This will be replaced with a redirection to the next question in subsequent development stages
        } else {
            return back()->with("error", "wrong-answer");
        }
    }
}
