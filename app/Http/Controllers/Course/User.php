<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class User extends Controller
{

    /**
     * This function allows a course owner to remove user's from their course. We assume
     * the user is the course owner as the route is protected by 'course.owner' middleware.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return Response The information to send back via the user's AJAX request.
     */
    public function remove ( Request $request, string $id )
    {
        // Validation checks
        try {
            $validatedData = $request->validate([
                'userId' => [ 'required', 'integer', 'exists:user_courses,id' ],
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
        if ( $userRecordQuery->exists() ) {
            $userRecord = $userRecordQuery->firstOrFail();
            if ( $userRecord->delete() ) {
                return response("User removal: success", 200);
            } else {
                return response("User removal: could not delete the record.", 500);
            }
        } else {
            return response("User removal: could not find the requested record", 404);
        }
    }

    /**
     * The block route will be used to execute logic to block a user from accessing a course. This
     * will not delete the user, rather, it will enable a switch in the database to stop the user
     * from being able to view the course.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return Response The response to send back via the user's AJAX request.
     */
    public function block ( Request $request, string $id )
    {
        // Validation checks
        try {
            $validatedData = $request->validate([
                'userId' => [ 'required', 'integer', 'exists:user_courses,id' ],
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
        if ( $userRecordQuery->exists() ) {
            $userRecord = $userRecordQuery->firstOrFail();
            $userRecord->blocked = !$userRecord->blocked;
            if ( $userRecord->save() ) {
                return response($userRecord->blocked, 200);
            } else {
                return response("User block toggle: could not update the record.", 500);
            }
        } else {
            return response("User block toggle: could not find the requested record", 404);
        }
    }

}
