<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Certain attributes can be casted to certain types to save programming time
     */
    protected $casts = [
        'item_value' => 'array',
    ];

    protected $fillable = [
        'position',
        'section_id'
    ];

    /*
     * Create a many-to-one relationship between the item and its owner
     */
    public function section()
    {
        return $this->hasOne(Section::class, 'id', 'section_id');
    }

    /*
     * A function to check if the lesson is valid before displaying it.
     * The name of the function is specified such that it can be used as
     * an accessor in views.
     */
    public function getLessonExistsAttribute()
    {
        return Lesson::where(['id' => $this->item_value['lesson_id']])->exists();
    }
}
