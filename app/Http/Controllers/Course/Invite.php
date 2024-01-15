<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\CourseInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class Invite extends Controller
{
    //
    public function show(Request $request)
    {
        // Check there is a valid invite
        $invite_id = $request->get('id');
        if (!$invite_id) {
            return redirect()->to(route('home'))->withErrors(['errorMessage' => 'An invitation ID could not be found, please try again or ask for another invitation link.']);
        }

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $invite_id)->first();
        if (!$invite) { // Check the invitation records
            return view('courses.invite', ['success' => false, 'errorMessage' => 'No record of this invitation could be found. Please try again or ask for another invitation.']);
        } elseif  (!$invite->is_active) {// Check the invite is active
            return view('courses.invite', ['success' => false, 'errorMessage' => 'This invite is inactive. Please try again or ask for another invitation.']);
        } elseif ($invite->expiry_date < now()) { // Check the invite is in date
            return view('courses.invite', ['success' => false, 'errorMessage' => 'This invite has expired. Please ask for another invitation.']);
        } elseif ($invite->uses >= $invite->max_uses) { // Check max uses
            return view('courses.invite', ['success' => false, 'errorMessage' => 'This invite has reached it\'s maximum number of uses. Please ask for another invite.']);
        }

        // Present the page if the invite is valid
        return view('courses.invite', ['success' => true, 'content' => $invite->course]);
    }

    public function accept()
    {

    }
}
