<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInvite extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Create a many-to-one relationship between the invite and the course
     * (many invites can belong to one course)
     */
    public function course()
    {
        return $this->hasOne(Course::class);
    }
}
