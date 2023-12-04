<?php

namespace App\Http\Controllers;

use App\Models\LessonItem;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

class Question extends Controller
{
    // Convert the question into the needed format
    private function formatQuestion($question)
    {
        if ($question && $question->item_value) {
            $question->item_value = Json::decode($question->item_value);
        }

        return $question;
    }

    // Create a temporary page to display the question
    public function index(string $id)
    {
        if ($id) {
            $question = LessonItem::where('id', $id)->firstOrFail();
            $question = $this->formatQuestion($question);

            return view('question', $question->toArray());
        }
    }

    // Create a route to process partial answers using AJAX requests
    public function partial(Request $request)
    {
        // Set up some required information to process the answer
        $validatedData = $request->validate([
            'question_id' => ['required'],
            'answer' => ['required']
        ]);

        // Get the details on the question
        $question = LessonItem::where('id', $validatedData['question_id'])->firstOrFail();
        $question = $this->formatQuestion($question);

        switch ($question->item_value['question_type']) {
            case "match":
                // Retrieve the question information
                $questionInfo = Json::decode('{
                    "question_type": "match",
                    "items_to_match": [
                        ["Variable", "A memory location that stores data"],
                        ["Function", "A block of code used to execute the same process repeatedly"],
                        ["Object", "An instance of a class"],
                        ["Class", "A template container for an object"],
                        ["Integer", "A variable used to store whole numbers"],
                        ["Float", "A variable used to store numbers with decimal places"]
                    ]
                }');

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
                $questionInfo = Json::decode('{
                    "question_type": "wordsearch",
                    "words": [
                        ["Variable", "A memory location that stores data"],
                        ["Function", "A block of code used to execute the same process repeatedly"],
                        ["Object", "An instance of a class"],
                        ["Class", "A template container for an object"],
                        ["Integer", "A variable used to store whole numbers"],
                        ["Float", "A variable used to store numbers with decimal places"]
                    ],
                    "are_sides_random": true
                }');

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

    // Write a function to process answers
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
        }

        if ($isAnswerCorrect) {
            return redirect()->to(url("correct")); // This will be replaced with a redirection to the next question in subsequent development stages
        } else {
            return back()->with("error", "wrong-answer");
        }
    }
}
