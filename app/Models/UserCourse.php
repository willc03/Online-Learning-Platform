<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;

    /*
     * Create a many-to-one relationship to connect the course to the model
     * (many course users can belong to a single course)
     */
    public function course()
    {
        return $this->hasOne(Course::class);
    }

    /*
     * Create a many-to-one relationship to connect the user to the model
     * (many course users can belong to a single user)
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
