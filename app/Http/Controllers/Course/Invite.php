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
        $invite = CourseInvite::find($invite_id);
        if (!$invite)
        {
            return view('courses.invite', ['success' => false, 'errorMessage' => 'No record of this invitation could be found. Please try again or ask for another invitation.']);
        }

        // Check the invite is active
        if (!$invite->is_active)
        {
            return redirect()->to(route('home'))->withErrors(['errorMessage' => 'This invite is inactive. Please try again or ask for another invitation.']);
        }

        // Check the invite is in date
        if ($invite->expiry_date->isPast())
        {
            return redirect()->to(route('home'))->withErrors(['errorMessage' => 'This invite has expired. Please ask for another invitation.']);
        }

        return $invite_id;
    }

    public function respond()
    {

    }

    public function create()
    {

    }

    public function manage()
    {

    }
}
