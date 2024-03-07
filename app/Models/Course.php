<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        return $this->hasMany(CourseInvite::class)->orderBy('created_at');
    }

    /*
     * Create a one-to-many relationship between a Course and its Sections
     * (one course can have many sections)
     */
    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('position');
    }

    /*
     * Create a one-to-many relationship between a Course and its Users
     */
    public function users()
    {
        return $this->hasMany(UserCourse::class)->orderBy('created_at');
    }

    /*
     * Create a one-to-many relationship between a Course and its Files
     * (one course can have many files)
     */
    public function files()
    {
        return $this->hasMany(CourseFile::class);
    }

    /**
     * A custom accessor is defined to get the course owner's information
     *
     * @return User|null
     */
    public function getCourseOwnerAttribute(): ?User
    {
        return User::find($this->owner);
    }

}
