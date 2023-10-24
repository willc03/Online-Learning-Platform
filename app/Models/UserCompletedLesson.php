<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCompletedLesson extends Model
{
    use HasFactory;

    /*
     * Create a many-to-one link between the completed lessons and the lesson itself
     * (many users can be logged as completed on one lesson)
     */
    public function lesson()
    {
        return $this->hasOne(Lesson::class);
    }

    /*
     * Create a many-to-one relationship between the user and the completed log
     * (many completed lessons can be logged from a single user)
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
