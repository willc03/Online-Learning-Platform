<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\CourseInvite;
use App\Models\UserCourse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Invite extends Controller
{

    /**
     * @param $courseId
     * @param $userId
     * @return bool
     */
    private function userExistsOnCourse($courseId, $userId)
    {
        return UserCourse::where(['course_id' => $courseId, 'user_id' => $userId])->exists();
    }

    /**
     * @param CourseInvite|null $invite
     * @return false|array
     */
    private function validate_invite(?CourseInvite $invite)
    {
        if (!$invite) {
            return ['success' => false, 'errorMessage' => 'No record of this invitation could be found. Please try again or ask for another invitation.'];
        }

        if (!$invite->is_active) {
            return ['success' => false, 'errorMessage' => 'This invite is inactive. Please try again or ask for another invitation.'];
        }

        if ($invite->expiry_date < now()) {
            return ['success' => false, 'errorMessage' => 'This invite has expired. Please ask for another invitation.'];
        }

        if ($invite->uses >= $invite->max_uses) {
            return ['success' => false, 'errorMessage' => 'This invite has reached its maximum number of uses. Please ask for another invite.'];
        }

        return false;
    }
    //

    /**
     * @param Request $request
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function show(Request $request)
    {
        // Check there is a valid invite
        if (!$invite_id = $request->id) {
            return redirect()->to(route('home'))->withErrors(['errorMessage' => 'An invitation ID could not be found, please try again or ask for another invitation link.']);
        }

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $invite_id)->first();
        $invalid_invite = $this->validate_invite($invite);

        // Check the user is not already a course member
        if (!$invalid_invite && $this->userExistsOnCourse($invite->course_id, $request->user()->id)) {
            return redirect()->to(route('home'))->withErrors(['COURSE_MEMBER' => 'You cannot join a course you already take!']);
        }

        // Return the correct view
        return $invalid_invite ? view('courses.invite', $invalid_invite) : view('courses.invite', ['success' => true, 'content' => $invite->course]);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function accept(Request $request)
    {
        // Check there is an ID in the request
        if (!$invite_id = $request->id) {
            return back();
        }

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $invite_id)->first();
        $invalid_invite = $this->validate_invite($invite);

        // Check the user is not already a course member
        if (!$invalid_invite && $this->userExistsOnCourse($invite->course_id, $request->user()->id)) {
            return redirect()->to(route('home'))->withErrors(['COURSE_MEMBER' => 'You cannot join a course you already take!']);
        }

        // Redirect the user accordingly
        if ($invalid_invite) {
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
}
