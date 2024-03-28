<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserCourse extends Model
{

    use HasUuids;

    /**
     * Create a many-to-one relationship to connect the course to the model
     * (many course users can belong to a single course)
     *
     * @return HasOne The relation is returned and applied to the Model.
     */
    public function course ()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    /**
     * Create a many-to-one relationship to connect the user to the model
     * (many course users can belong to a single user)
     *
     * @return HasOne The relation is returned and applied to the Model.
     */
    public function user ()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
