<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use LaravelIdea\Helper\App\Models\_IH_Course_C;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'name'              => 'encrypted',
        'email'             => 'encrypted',
    ];

    /**
     * Define a one-to-many relationship between the user and their courses
     * (one user can take many courses)
     *
     * @return BelongsToMany The relation is returned and applied to the Model.
     */
    public function courses ()
    {
        return $this->belongsToMany(Course::class, 'user_courses', 'user_id', 'course_id');
    }

    /**
     * Define a one-to-many relationship between the user and courses they
     * own.
     *
     * @return HasMany The relation is returned and applied to the Model.
     */
    public function ownedCourses ()
    {
        return $this->hasMany(Course::class, 'owner', 'id');
    }

    /**
     * Define a function to get the courses the user is in AND owns.
     *
     * @return Course[]|_IH_Course_C|mixed
     */
    public function getDisplayableCoursesAttribute ()
    {
        return $this->ownedCourses->merge($this->courses)->unique('id');
    }

}
