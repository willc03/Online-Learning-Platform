<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /*
         * Define the default password rules
         */
        Password::defaults( function() {
            // Consider adding the uncompromised attribute in the future.
            return Password::min(8)->symbols()->letters()->mixedCase()->numbers(); // At least 1 number, symbol, uppercase, lowercase & at least 8 characters long
        });

        /*
         * Define a gate for file upload permissions
         */
        Gate::define('file-upload', function(User $user, Course $course) {
            return $user->id === $course->owner;
        });

        /*
         * Define a gate for course editing
         */
        Gate::define('course-edit', function(User $user, Course $course) {
            return $user->id === $course->owner;
        });
    }
}
