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
        return view('question', [
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
        ]);
    }
}
