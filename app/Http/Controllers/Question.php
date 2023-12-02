<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

class Question extends Controller
{
    // Create a temporary page to display the question
    public function index()
    {
        /* SINGLE CHOICE return view('question', [
            'id' => 1,
            'position' => 1,
            'item_title' => 'What is the cube root of 64?',
            'description' => null,
            'item_type' => 'question',
            'item_value' => Json::decode('{
                "question_type":"single_choice",
                "question_choices": [4,8,16,48],
                "correct_answer": 4,
                "one_time_answer": false
            }')
        ]); */
        /* MULTI CHOICE return view('question', [
            'id' => 1,
            'position' => 1,
            'item_title' => 'Which of these are prime numbers?',
            'description' => null,
            'item_type' => 'question',
            'item_value' => Json::decode('{
                "question_type":"multiple_choice",
                "question_choices": [2,3,9,5,17,31,99],
                "correct_answers": [2,3,5,17,31]
            }')
        ]); */
        /* FILL IN THE BLANKS return view('question', [
            'id' => 1,
            'position' => 1,
            'item_title' => 'Because % the equipment % is very delicate, it must be handled with %',
            'description' => null,
            'item_type' => 'question',
            'item_value' => Json::decode('{
                "question_type": "fill_in_blanks",
                "question_choices": ["care", "carefully", "caring", "careful"],
                "correct_answers": ["care"]
            }')
        ]); */
        /* TRUE OR FALSE return view('question', [
            'id' => 1,
            'position' => 1,
            'item_title' => 'The year 2000 was a leap year.',
            'description' => null,
            'item_type' => 'question',
            'item_value' => Json::decode('{
                "question_type": "true_or_false",
                "one_time_answer": true,
                "correct_answer": true
            }')
        ]); */
        /* CORRECT ORDER return view('question', [
            'id' => 1,
            'position' => 1,
            'item_title' => 'Drag the below C++ code into the correct order to carry out SELECTION.',
            'description' => null,
            'item_type' => 'question',
            'item_value' => Json::decode('{
                "question_type": "order",
                "answer_slots": ["bool isEven = false;", "if (2 % 0 == 0) {", "    isEven = true;", "}"],
                "correct_answer": ["bool isEven = false;", "if (2 % 0 == 0) {", "    isEven = true;", "}"],
                "direction": "vertical"
            }')
        ]); */
        /* MATCH return view('question', [
            'id' => 1,
            'position' => 1,
            'item_title' => 'Match the following words and their definitions.',
            'description' => null,
            'item_type' => 'question',
            'item_value' => Json::decode('{
                "question_type": "match",
                "items_to_match": [
                    ["Variable", "A memory location that stores data"],
                    ["Function", "A block of code used to execute the same process repeatedly"],
                    ["Object", "An instance of a class"],
                    ["Class", "A template container for an object"],
                    ["Integer", "A variable used to store whole numbers"],
                    ["Float", "A variable used to store numbers with decimal places"]
                ],
                "are_sides_random": true
            }')
        ]); */
        return view('question', [
            'id' => 1,
            'position' => 1,
            'item_title' => 'Find the words to get their definitions.',
            'description' => null,
            'item_type' => 'question',
            'item_value' => Json::decode('{
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
            }')
        ]);
    }

    // Create a route to process partial answers using AJAX requests
    // THIS IS ONLY TEMPORARY AS NOTHING IS YET STORED IN THE DATABASE
    public function partial(Request $request)
    {
        // Temporary question type variable before this is linked with the database
        $question_type_temp = "wordsearch";

        // Set up some required information to process the answer
        $validated_data = $request->validate([
            'question_id' => ['required'],
            'answer' => ['required']
        ]);

        switch ($question_type_temp) {
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
