<?php

namespace App\Http\Controllers\Course;

use App\Models\Course;
use App\Http\Controllers\Controller;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class User extends Controller
{
    /**
     * The remove function will be used to execute logic to remove a user from a course.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function remove ( Request $request, string $id )
    {
        // Validation checks
        try {
            $validatedData = $request->validate([
                'userId' => [ 'required', 'integer', 'exists:user_courses,id' ]
            ]);
        } catch ( ValidationException $exception ) {
            return response("User removal: validation failed.", 403);
        }
        // Course ownership safety check
        $course = Course::whereId($id)->firstOrFail();
        if ( !Gate::allows('course-edit', $course) ) {
            return response("User removal: unauthorised action.", 403);
        }
        // Run removal
        $userRecordQuery = UserCourse::whereId($validatedData['userId']);
        if ($userRecordQuery->exists()) {
            $userRecord = $userRecordQuery->firstOrFail();
            if ($userRecord->delete()) {
                return response("User removal: success", 200);
            } else {
                return response("User removal: could not delete the record.", 500);
            }
        } else {
            return response("User removal: could not find the requested record", 404);
        }
    }

    /**
     * The block function will be used to execute logic to block a user from accessing a course.
     * This will not delete the user, rather, it will enable a switch in the database to stop the
     * user from being able to view the course.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function block (Request $request, $id)
    {
        // Validation checks
        try {
            $validatedData = $request->validate([
                'userId' => [ 'required', 'integer', 'exists:user_courses,id' ]
            ]);
        } catch ( ValidationException $exception ) {
            return response("User block toggle: validation failed.", 403);
        }
        // Course ownership safety check
        $course = Course::whereId($id)->firstOrFail();
        if ( !Gate::allows('course-edit', $course) ) {
            return response("User block toggle: unauthorised action.", 403);
        }
        // Run removal
        $userRecordQuery = UserCourse::whereId($validatedData['userId']);
        if ($userRecordQuery->exists()) {
            $userRecord = $userRecordQuery->firstOrFail();
            $userRecord->blocked = !$userRecord->blocked;
            if ($userRecord->save()) {
                return response($userRecord->blocked, 200);
            } else {
                return response("User block toggle: could not update the record.", 500);
            }
        } else {
            return response("User block toggle: could not find the requested record", 404);
        }
    }
}
