<?php

namespace App\Http\Middleware;

use App\Models\Course;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourseOwner
{

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle ( Request $request, Closure $next ) : Response
    {
        // Get the course id from the URL
        $urlCourseId = explode('/', preg_replace("#^[^:/.]*[:/]+#i", "", url()->current()))[2];
        // Get the course from the id
        $courseQuery = Course::where('id', $urlCourseId);
        // Check the course exists
        if ( !$courseQuery->exists() ) {
            return redirect()->to(route('home'))->withErrors([ 'INVALID_COURSE' => 'The specified course does not exist. Please try again.' ]);
        }
        // Return a redirect or allow the request based on whether the user is part of the course.
        return ( $courseQuery->firstOrFail()->owner === $request->user()->id ) ? $next($request) : redirect()->to(route('course.home', [ 'id' => $urlCourseId ]))->withErrors([ 'NOT_COURSE_OWNER' => 'You must be the owner of this course to access this page.' ]);
    }

}
