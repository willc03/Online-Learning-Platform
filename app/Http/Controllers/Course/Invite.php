<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseInvite;
use App\Models\UserCourse;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

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
            return redirect()->to(route('home'))->withErrors([ 'errorMessage' => 'The invitation link is invalid, please try again or ask for another invitation link.' ]);
        }

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $invite_id)->first();
        $invalid_invite = $this->checkInviteValidity($invite);

        // Check the user is not already a course member
        if ( !$invalid_invite && $this->userTakesCourse($invite->course_id, $request->user()->id) ) {
            return redirect()->to(route('course.home', [ 'id' => $invite->course->id ]))->withErrors([ 'COURSE_MEMBER' => 'You cannot join a course you already take!' ]);
        }

        // Check the user does not own the course
        if ( $invite->course->owner == $request->user()->id ) {
            return redirect()->to(route('course.home', [ 'id' => $invite->course->id ]))->withErrors([ 'COURSE_OWNER' => 'You cannot join a course you own!' ]);
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
            'modificationType' => [ 'required', 'string', 'in:activeState,maxUses,expiryDate' ],
            'newMax' => [ 'nullable', 'numeric' ],
            'newDate' => [ 'nullable', 'date_format:d/m/Y H:i' ]
        ]);
        // Course edit ability check
        $course = Course::where([ 'id' => $id ])->firstOrFail();
        if ( !Gate::allows('course-edit', $course) ) {
            return response("You do not have permission to edit invites for this course!", 403);
        }
        // Get the invite
        $invite = CourseInvite::where([ 'invite_id' => $validatedData['inviteId'] ])->firstOrFail();
        // Switch case for execution logic
        switch ( $validatedData['modificationType'] ) {
            case "activeState":
                $invite->is_active = !$invite->is_active;
                if ( !$invite->save() ) {
                    return response("Could not save database record", 500);
                } else {
                    return response($invite->is_active, 200);
                }
                break;
            case "maxUses":
                // Validation
                $maxUseValidation = Validator::make($validatedData, [
                    'newMax' => [ 'required', 'numeric', 'min:' . $invite->uses ?? 0 ]
                ]);
                if ( $maxUseValidation->fails() ) {
                    return response("Validation failed.", 400);
                }
                // Make changes
                $invite->max_uses = $validatedData['newMax'];
                if ( !$invite->save() ) {
                    return response("Could not update the maximum uses.", 500);
                } else {
                    return response("Successfully changed max uses to " . $validatedData['newMax'], 200);
                }
                break;
            case "expiryDate":
                // Expiry date validation
                $expiryDateValidation = Validator::make($validatedData, [
                    'newDate' => [ 'required', 'date_format:d/m/Y H:i' ]
                ]);
                if ( $expiryDateValidation->fails() ) {
                    return response("Validation failed.", 400);
                }
                // Format date
                $validatedData['newDate'] = Carbon::createFromFormat('d/m/Y H:i', $validatedData['newDate']);
                // Make changes
                $invite->expiry_date = $validatedData['newDate'];
                if ( !$invite->save() ) {
                    return response("Could not update the expiry date", 500);
                } else {
                    return response("Successfully updated the expiry date (new date: " . $validatedData['newDate']->format('d/m/Y H:i'));
                }
                break;
            default:
                return response("Execution took an unexpected path", 500);
                break;
        }
    }
}
