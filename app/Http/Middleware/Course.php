<?php

namespace App\Http\Middleware;

use App\Models\UserCourse;
use App\Models\Course as CourseModel;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Course
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $urlCourseId = explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2];

        $courseQuery = CourseModel::where('id', $urlCourseId);

        if (!$courseQuery->exists()) {
            return redirect()->to(route('home'))->withErrors(['INVALID_COURSE' => 'The specified course does not exist. Please try again.']);
        }

        $userTakesCourse = UserCourse::where(['course_id' => $urlCourseId, 'user_id' => $request->user()->id])->exists();
        $userOwnsCourse = $courseQuery->firstOrFail()->owner === $request->user()->id;

        return ($userTakesCourse || $userOwnsCourse) ? $next($request) : redirect()->to(route('home'))->withErrors(['NOT_COURSE_MEMBER' => 'Please join this course to access its pages.']);
    }
}
