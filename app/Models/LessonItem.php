<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonItem extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Certain attributes can be casted to certain types to save programming time
     */
    protected $casts = [
        'item_value' => 'array',
    ];

    /*
     * Create a relationship to link the lesson items and its lesson
     * (many lesson items can belong to a single lesson)
     */
    public function lesson()
    {
        return $this->hasOne(Lesson::class);
    }
}
