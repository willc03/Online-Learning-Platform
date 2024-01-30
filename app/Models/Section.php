<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Create a many-to-one relationship between the section and its course
     * (many sections can belong to a course)
     */
    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    /*
     * Create a one-to-many relationship between a section and its items
     * (one section can have many items)
     */
    public function items()
    {
        return $this->hasMany(SectionItem::class, 'section_id', 'id')->orderBy('position');
    }
}
