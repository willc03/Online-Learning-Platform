<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFile extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Create a many-to-one relationship with the course file and its course
     */
    public function course()
    {
        return $this->hasOne(Course::class);
    }
}
