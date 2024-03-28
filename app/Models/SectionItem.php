<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SectionItem extends Model
{

    use HasFactory;
    use HasUuids;

    /**
     * Certain attributes can be casted to certain types to save programming time. Values
     * are automatically converted when sent and retrieved from the database.
     *
     * @var string[] The list of casts
     */
    protected $casts = [
        'item_value' => 'array',
    ];

    /**
     * Attributes that can be mass-assigned are placed in this protected attribute.
     *
     * @var string[] The list of fillable attributes using methods such as create or update
     */
    protected $fillable = [
        'position',
        'section_id',
    ];

    /**
     * Create a many-to-one relationship between the item and its owner.
     *
     * @return HasOne The relation is returned and applied to the Model.
     */
    public function section ()
    {
        return $this->hasOne(Section::class, 'id', 'section_id');
    }

    /**
     * A function to check if the lesson is valid before displaying it.
     * The name of the function is specified such that it can be used as
     * an accessor in views.
     *
     * @return bool Whether the intended model exists.
     */
    public function getLessonExistsAttribute ()
    {
        return Lesson::where([ 'id' => $this->item_value['lesson_id'] ])->exists();
    }

}
