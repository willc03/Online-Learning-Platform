<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Create a relationship to connect the lesson and its items
     */
    public function items()
    {
        return $this->hasMany(LessonItem::class);
    }

    public function users_completed()
    {
        return $this->hasMany(UserCompletedLesson::class);
    }
}
