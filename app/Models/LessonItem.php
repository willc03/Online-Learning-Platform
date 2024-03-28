<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LessonItem extends Model
{

    use HasFactory;
    use HasUuids;

    /**
     * Certain attributes can be cast to certain types to save programming time.
     * Values are automatically converted when sent and retrieved from the database.
     *
     * @var string[]
     */
    protected $casts = [
        'item_value' => 'array',
    ];

    /**
     * Attributes that can be mass-assigned are placed in this protected attribute.
     *
     * @var string[] The list of fillable attributes using methods such as create or update.
     */
    protected $fillable = [
        'position',
    ];

    /**
     * Create a relationship to link the lesson items and its lesson
     * (many lesson items can belong to a single lesson)
     *
     * @return HasOne The relation is returned to be applied to the Model.
     */
    public function lesson ()
    {
        return $this->hasOne(Lesson::class, 'id', 'lesson_id');
    }

}
