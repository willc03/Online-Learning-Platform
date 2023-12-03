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
    // THIS IS ONLY TEMPORARY AS NOTHING IS YET STORED IN THE DATABASE
    public function partial(Request $request)
    {
        // Set up some required information to process the answer
        $validated_data = $request->validate([
            'question_id' => ['required'],
            'answer' => ['required']
        ]);

        // Get the details on the question
        $question = LessonItem::where('id', $validated_data['question_id'])->firstOrFail();
        $question = $this->formatQuestion($question);

        switch ($question->item_value['question_type']) {
            case "match":
                // Retrieve the question information
                $question_info = Json::decode('{
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
                for ($i = 0; $i < count($question_info['items_to_match']); $i++) {
                    if (count( array_intersect($question_info["items_to_match"][$i], $validated_data['answer']) ) == count($validated_data['answer'])) {
                        return 'true';
                    }
                }
                return 'false';
                break;

            case "wordsearch":
                // Get the question info
                $question_info = Json::decode('{
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
                $user_word = strtolower(implode($validated_data['answer']));
                for ($i = 0; $i < count($question_info['words']); $i++) {
                    if ( $user_word == strtolower($question_info["words"][$i][0]) || strrev($user_word) == strtolower($question_info["words"][$i][0]) ) {
                        return $question_info['words'][$i];
                    }
                }
                return 'false';
                break;
        }

        return null;
    }
}
