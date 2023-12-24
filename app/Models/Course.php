<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Create a one-to-many relationship between a Course and its Invites
     * (one course can have many invites)
     */
    public function invites()
    {
        return $this->hasMany(CourseInvite::class);
    }

    /*
     * Create a one-to-many relationship between a Course and its Sections
     * (one course can have many sections)
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /*
     * Create a one-to-many relationship between a Course and its Users
     */
    public function users()
    {
        return $this->hasMany(UserCourse::class);
    }

    /*
     * Create a one-to-many relationship between a Course and its Files
     * (one course can have many files)
     */
    public function files()
    {
        return $this->hasMany(CourseFile::class);
    }
}
