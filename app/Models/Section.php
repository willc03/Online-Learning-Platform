<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Section extends Model
{

    use HasFactory;
    use HasUuids;

    /**
     * Attributes that can be mass-assigned are placed in this attribute.
     *
     * @var string[] The list of fillable attributes using methods such as create or update.
     */
    protected $fillable = [
        'position',
        'title',
        'description',
    ];

    /**
     * Create a many-to-one relationship between the section and its course
     * (many sections can belong to a course)
     *
     * @return HasOne The relation is returned to be applied to the Model.
     */
    public function course ()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    /**
     * Create a one-to-many relationship between a section and its items
     * (one section can have many items)
     *
     * @return HasMany The relation is returned to be applied to the Model.
     */
    public function items ()
    {
        return $this->hasMany(SectionItem::class, 'section_id', 'id')->orderBy('position');
    }

}
