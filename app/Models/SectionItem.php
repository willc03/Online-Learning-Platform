<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    use HasFactory;

    /*
     * Create a many-to-one relationship between the item and its owner
     */
    public function section()
    {
        return $this->hasOne(Section::class);
    }

    /*
     * Create a one-to-one relationship between the item and a lesson (if any)
     * One section can have one lesson
     */
    public function lesson()
    {
        return $this->hasOne(Lesson::class);
    }
}
