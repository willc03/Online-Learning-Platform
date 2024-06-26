<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserCompletedLesson extends Model
{

    use HasFactory;
    use HasUuids;

    /**
     * Create a many-to-one link between the completed lessons and the lesson itself
     * (many users can be logged as completed on one lesson)
     *
     * @return HasOne The relation is returned and applied to the Model.
     */
    public function lesson ()
    {
        return $this->hasOne(Lesson::class);
    }

    /**
     * Create a many-to-one relationship between the user and the completed log
     * (many completed lessons can be logged from a single user)
     *
     * @return HasOne The relation is returned and applied to the Model.
     */
    public function user ()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
