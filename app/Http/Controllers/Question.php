<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

class Question extends Controller
{
    // Create a temporary page to display the question
    public function index()
    {
        return view('question', [
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
        ]);
    }
}
