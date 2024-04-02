<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseInvite extends Model
{

    use HasFactory;
    use HasUuids;

    /**
     * Allow dates to be converted to the intended format automatically
     *
     * @var string[]
     */
    protected $casts = [
        'expiry_date' => 'datetime:Y-m-d H:i'
    ];

    /**
     * Create a many-to-one relationship between the invite and the course
     * (many invites can belong to one course)
     *
     * @return HasOne The relation is returned to be applied to the Model.
     */
    public function course ()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

}
