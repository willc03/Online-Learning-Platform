<?php

namespace App\Http\Middleware;

use App\Models\Course as CourseModel;
use App\Models\UserCourse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Course
{

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle ( Request $request, Closure $next ) : Response
    {
        // Get the course id from the url
        $urlCourseId = explode('/', preg_replace("#^[^:/.]*[:/]+#i", "", url()->current()))[2];
        // Get the course record by the id
        $courseQuery = CourseModel::where('id', $urlCourseId);
        // Check the course exists.
        if ( !$courseQuery->exists() ) {
            return redirect()->to(route('home'))->withErrors([ 'INVALID_COURSE' => 'The specified course does not exist. Please try again.' ]);
        }
        // Get details about the course
        $userCourseQuery = UserCourse::where([ 'course_id' => $urlCourseId, 'user_id' => $request->user()->id ]);
        $userTakesCourse = $userCourseQuery->exists();
        $userOwnsCourse = $courseQuery->firstOrFail()->owner === $request->user()->id;
        // Redirect the user if they're blocked.
        if ( $userTakesCourse && $userCourseQuery->firstOrFail()->blocked ) {
            return redirect()->to(route('home'))->withErrors([ 'BLOCKED' => 'You are blocked from viewing the contents of this course' ]);
        }
        // Redirect the user based on results
        return ( $userTakesCourse || $userOwnsCourse ) ? $next($request) : redirect()->to(route('home'))->withErrors([ 'NOT_COURSE_MEMBER' => 'Please join this course to access its pages.' ]);
    }

}
