<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonItem extends Model
{
    use HasFactory;

    /*
     * Create a relationship to link the lesson items and its lesson
     * (many lesson items can belong to a single lesson)
     */
    public function lesson()
    {
        return $this->hasOne(Lesson::class);
    }
}
