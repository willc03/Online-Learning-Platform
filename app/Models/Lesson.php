<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{

    use HasFactory;
    use HasUuids;

    /**
     * Create a relationship to connect the lesson and its items.
     *
     * @return HasMany The relation is returned to be applied to the Model.
     */
    public function items ()
    {
        return $this->hasMany(LessonItem::class, 'lesson_id', 'id')->orderBy('position');
    }

    /**
     * Create a relationship to connect a lesson and its attempts made by users.
     *
     * @return HasMany The relation is returned to be applied to the Model.
     */
    public function attempts ()
    {
        return $this->hasMany(UserCompletedLesson::class)->orderBy('created_at');
    }

}
