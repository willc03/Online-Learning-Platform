<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\CourseInvite;
use App\Models\UserCourse;
use App\Models\Course;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class Invite extends Controller
{

    /**
     * This function executes logic to present the user with a screen for accepting invites or returning to home based on database results.
     *
     * @param Request $request
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function show ( Request $request )
    {
        // Check there is a valid invite
        if ( !$invite_id = $request->id ) {
            return redirect()->to(route('home'))->withErrors([ 'errorMessage' => 'An invitation ID could not be found, please try again or ask for another invitation link.' ]);
        }

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $invite_id)->first();
        $invalid_invite = $this->checkInviteValidity($invite);

        // Check the user is not already a course member
        if ( !$invalid_invite && $this->userTakesCourse($invite->course_id, $request->user()->id) ) {
            return redirect()->to(route('home'))->withErrors([ 'COURSE_MEMBER' => 'You cannot join a course you already take!' ]);
        }

        // Return the correct view
        return $invalid_invite ? view('courses.invite', $invalid_invite) : view('courses.invite', [ 'success' => true, 'content' => $invite->course ]);
    }

    /**
     * This function will validate the invite against a number of database factors.
     *
     * @param CourseInvite|null $invite
     *
     * @return false|array
     */
    private function checkInviteValidity ( ?CourseInvite $invite )
    {
        if ( !$invite ) {
            return [ 'success' => false, 'errorMessage' => 'No record of this invitation could be found. Please try again or ask for another invitation.' ];
        }

        if ( !$invite->is_active ) {
            return [ 'success' => false, 'errorMessage' => 'This invite is inactive. Please try again or ask for another invitation.' ];
        }

        if ( $invite->expiry_date < now() ) {
            return [ 'success' => false, 'errorMessage' => 'This invite has expired. Please ask for another invitation.' ];
        }

        if ( $invite->uses >= $invite->max_uses ) {
            return [ 'success' => false, 'errorMessage' => 'This invite has reached its maximum number of uses. Please ask for another invite.' ];
        }

        return false;
    }
    //

    /**
     * This function accepts a course ID and user ID to deduce whether they are already a member of a course.
     *
     * @param $courseId
     * @param $userId
     *
     * @return bool
     */
    private function userTakesCourse ( $courseId, $userId )
    {
        return UserCourse::where([ 'course_id' => $courseId, 'user_id' => $userId ])->exists();
    }

    /**
     * This function accepts POST requests such that users can accept invites to join a course.
     *
     * @param Request $request
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function accept ( Request $request )
    {
        // Check there is an ID in the request
        if ( !$invite_id = $request->id ) {
            return back();
        }

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $invite_id)->first();
        $invalid_invite = $this->checkInviteValidity($invite);

        // Check the user is not already a course member
        if ( !$invalid_invite && $this->userTakesCourse($invite->course_id, $request->user()->id) ) {
            return redirect()->to(route('home'))->withErrors([ 'COURSE_MEMBER' => 'You cannot join a course you already take!' ]);
        }

        // Redirect the user accordingly
        if ( $invalid_invite ) {
            return view('courses.invite', $invalid_invite);
        } else {
            // Increase the number of uses
            $invite->uses++;

            // Add the user to the course
            UserCourse::insert([
                'course_id' => $invite->course_id,
                'user_id' => $request->user()->id
            ]);

            // Save the record
            $invite->save();

            // Redirect the user to the course
            return redirect()->to(url('/course/' . $invite->course_id));
        }
    }

    /**
     * This function will be used to process invite modifications
     *
     * @param Request $request
     * @param String  $id
     *
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function modify ( Request $request, string $id )
    {
        // Initial validation
        $validatedData = $request->validate([
            'inviteId' => [ 'required', 'string', 'exists:course_invites,invite_id' ],
            'modificationType' => [ 'required', 'string', 'in:activeState' ]
        ]);
        // Course edit ability check
        $course = Course::where([ 'id' => $id ])->firstOrFail();
        if ( !Gate::allows('course-edit', $course) ) {
            return response("You do not have permission to edit invites for this course!", 403);
        }
        // Switch case for execution logic
        switch ( $validatedData['modificationType'] ) {
            case "activeState":
                $invite = CourseInvite::where([ 'invite_id' => $validatedData['inviteId'] ])->firstOrFail();
                $invite->is_active = !$invite->is_active;
                if ( !$invite->save() ) {
                    return response("Could not save database record", 500);
                } else {
                    return response($invite->is_active, 200);
                }
                break;
            default:
                return response("Execution took an unexpected path", 500);
        }
    }
}
