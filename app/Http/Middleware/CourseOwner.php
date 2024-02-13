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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $urlCourseId = explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2];

        $courseQuery = Course::where('id', $urlCourseId);

        if (!$courseQuery->exists()) {
            return redirect()->to(route('home'))->withErrors(['INVALID_COURSE' => 'The specified course does not exist. Please try again.']);
        }

        return ($courseQuery->firstOrFail()->owner === $request->user()->id) ? $next($request) : redirect()->to(route('home'))->withErrors(['NOT_COURSE_OWNER' => 'You must be the owner of this course to access this page.']);
    }
}
