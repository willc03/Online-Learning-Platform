<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseInvite;
use App\Models\UserCourse;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Invite extends Controller
{

    /**
     * This function executes logic to present the user with a screen for accepting invites or returning
     * to home based on database results.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return View|RedirectResponse The view to show the user (if successful) or the redirection
     */
    public function show ( Request $request )
    {
        // Validate the request
        $validatedData = $request->validate([
            'id' => [ 'required', 'string', 'exists:course_invites,id' ],
        ]);

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $validatedData['id'])->firstOrFail();
        $inviteValidity = $this->validateInvite($invite);

        // Check the course isn't public
        $course = Course::whereId($invite->course_id);
        if ( $course->exists() && $course->firstOrFail()->is_public ) {
            return redirect()->to(route('courses'))->withErrors([ 'COURSE_PUBLIC' => 'Course invitations cannot be used on public courses. Please find the course on this page.' ]);
        }

        // Check the user is not already a course member
        if ( !$inviteValidity && $this->userTakesCourse($invite->course_id, $request->user()->id) ) {
            return redirect()->to(route('course.home', [ 'id' => $invite->course->id ]))->withErrors([ 'COURSE_MEMBER' => 'You cannot join a course you already take!' ]);
        }

        // Check the user does not own the course
        if ( $invite->course->owner == $request->user()->id ) {
            return redirect()->to(route('course.home', [ 'id' => $invite->course->id ]))->withErrors([ 'COURSE_OWNER' => 'You cannot join a course you own!' ]);
        }

        // Return the correct view
        return $inviteValidity ? view('courses.invite', $inviteValidity) : view('courses.invite', [ 'success' => true, 'content' => $invite->course ]);
    }

    /**
     * This function will validate the invite against a number of database factors.
     *
     * @param CourseInvite|null $invite The course invite model instance (if one is passed in)
     *
     * @return false|array Information that can be used to take paths in certain routes
     */
    private function validateInvite ( ?CourseInvite $invite )
    {
        if ( !$invite ) { // Check there is an invite that exists.
            return [ 'success' => false, 'errorMessage' => 'No record of this invitation could be found. Please try again or ask for another invitation.' ];
        }

        if ( !$invite->is_active ) { // Check the invite is active.
            return [ 'success' => false, 'errorMessage' => 'This invite is inactive. Please try again or ask for another invitation.' ];
        }

        if ( $invite->expiry_date < now() && $invite->expiry_date != null ) { // Check the invite is in date
            return [ 'success' => false, 'errorMessage' => 'This invite has expired. Please ask for another invitation.' ];
        }

        if ( $invite->uses >= $invite->max_uses && $invite->max_uses != null ) { // Check the invite has not exceeded its maximum uses.
            return [ 'success' => false, 'errorMessage' => 'This invite has reached its maximum number of uses. Please ask for another invite.' ];
        }

        return false; // Return false as a fail-safe
    }

    /**
     * This function accepts a course ID and user ID to deduce whether they are already a member of a course.
     *
     * @param string $courseId The course's id (UUID)
     * @param int    $userId   The user's id (int)
     *
     * @return bool
     */
    private function userTakesCourse ( string $courseId, int $userId )
    {
        return UserCourse::where([ 'course_id' => $courseId, 'user_id' => $userId ])->exists();
    }

    /**
     * This function accepts POST requests such that users can accept invites to join a course.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return View|RedirectResponse The view or redirect to show the user
     */
    public function accept ( Request $request )
    {
        // Check there is an ID in the request
        if ( !$invite_id = $request->id ) {
            return back();
        }

        // If the ID is a course ID and the course is public, accept the request immediately.
        $course = Course::where('id', $request->id);
        if ( $course->exists() && $course->firstOrFail()->is_public ) {
            $validatedData = $request->validate([ 'id' => [ 'required', 'string', 'exists:courses,id' ] ]);

            $userCourseRecord = new UserCourse;
            $userCourseRecord->course_id = $validatedData['id'];
            $userCourseRecord->user_id = $request->user()->id;
            $userCourseRecord->id = UserCourse::all()->count() + 1;

            if ( $userCourseRecord->save() ) {
                return redirect()->to(route('course.home', [ 'id' => $invite_id ])); // Send the user to the accepted course's home page.
            } else {
                return back()->withErrors([ 'RECORD_SAVE_ERROR' => 'The system could not add you to the course. Please try again!' ]); // Return the user back with an error message
            }
        }

        // Check the invite is valid
        $invite = CourseInvite::where('invite_id', $invite_id)->first();
        $invalid_invite = $this->validateInvite($invite);

        // Check the user is not already a course member
        if ( !$invalid_invite && $this->userTakesCourse($invite->course_id, $request->user()->id) ) {
            return redirect()->to(route('home'))->withErrors([ 'COURSE_MEMBER' => 'You cannot join a course you already take!' ]);
        }

        // Redirect the user accordingly
        if ( $invalid_invite ) {
            return view('courses.invite', $invalid_invite);
        } else {
            // Increase the number of uses
            $invite->increment('uses', 1);

            // Add the user to the course
            $userCourseRecord = new UserCourse;
            $userCourseRecord->course_id = $invite->course_id;
            $userCourseRecord->user_id = $request->user()->id;
            $userCourseRecord->id = UserCourse::all()->count() + 1;
            $userCourseRecord->save();

            // Redirect the user to the course
            return redirect()->to(url('/course/' . $invite->course_id));
        }
    }

    /**
     * This route processes invite modification requests, allowing the user to change aspects such as
     * the active state, expiry date, and maximum uses.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function modify ( Request $request, string $id )
    {
        // We can assume the user is able to edit the course as the route is protected by middleware
        // Initial validation
        $validatedData = $request->validate([
            'inviteId'         => [ 'required', 'string', 'exists:course_invites,invite_id' ],   // Make sure the invite exists
            'modificationType' => [ 'required', 'string', 'in:activeState,maxUses,expiryDate' ], // Only allow the set modification types
            'newMax'           => [ 'nullable', 'numeric' ],                                     // Accept the new maximum date
            'newDate'          => [ 'nullable', 'date_format:d/m/Y H:i' ],                       // Accept the new date
            'remove'           => [ 'nullable', 'integer' ],                                     // A flag on whether to remove the date or max uses.
        ]);
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
                    'newMax' => [ Rule::requiredIf(!array_key_exists('remove', $validatedData)), Rule::excludeIf(array_key_exists('remove', $validatedData)), 'numeric', 'min:' . $invite->uses ?? 0 ],
                    'remove' => [ 'nullable', 'integer' ],
                ]);
                if ( $maxUseValidation->fails() ) {
                    return response($maxUseValidation->errors(), 400);
                }
                // Make changes
                $invite->max_uses = array_key_exists('remove', $validatedData) ? null : $validatedData['newMax'];
                if ( !$invite->save() ) {
                    return response("Could not update the maximum uses.", 500);
                } else {
                    return response("Successfully changed max uses to " . $validatedData['newMax'], 200);
                }
                break;
            case "expiryDate":
                // Expiry date validation
                $expiryDateValidation = Validator::make($validatedData, [
                    'newDate' => [ Rule::requiredIf(!array_key_exists('remove', $validatedData)), Rule::excludeIf(array_key_exists('remove', $validatedData)), 'required', 'date_format:d/m/Y H:i' ],
                    'remove'  => [ 'nullable', 'integer' ],
                ]);
                if ( $expiryDateValidation->fails() ) {
                    return response("Validation failed.", 400);
                }
                // Format date
                $validatedData['newDate'] = Carbon::createFromFormat('d/m/Y H:i', $validatedData['newDate']);
                // Make changes
                $invite->expiry_date = array_key_exists('remove', $validatedData) ? null : $validatedData['newDate'];
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

    /**
     * A DELETE route that allows invites to be deleted by course owners.
     *
     * @param Request $request The HTTP request provided by Laravel.
     * @param string  $id      The course's id (UUID)
     *
     * @return Response The response to inform the client on success.
     */
    public function delete ( Request $request, string $id )
    {
        // We can assume the user can delete invites as the route is protected
        // Validation of uploaded data
        $validatedData = $request->validate([
            'inviteId' => [ 'required', 'string', 'exists:course_invites,invite_id' ],
        ]);
        // Delete the invite if the user has permission
        $invite = CourseInvite::where([ 'invite_id' => $validatedData['inviteId'] ])->firstOrFail();
        if ( $invite->delete() ) {
            return response("Invite successfully deleted", 200);
        } else {
            return response("Could not delete the invite", 500);
        }
    }

    /**
     * A route to allow course owners to create new invites (if the course is private)
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return RedirectResponse The redirect based on whether the request is successful
     */
    public function create ( Request $request, string $id )
    {
        // We can assume the user is the course owner
        // Validation of uploaded data
        $validatedData = $request->validate([
            'active'        => [ 'nullable', 'string', 'in:on' ],
            'unlimitedUses' => [ 'nullable', 'string', 'in:on' ],
            'allowedUses'   => [ Rule::excludeIf(fn () => $request->unlimitedUses !== null), 'nullable', 'integer', 'min:1', Rule::requiredIf(fn () => $request->unlimitedUses === null) ],
            'neverExpire'   => [ 'nullable', 'string', 'in:on' ],
            'expiryDate'    => [ Rule::excludeIf(fn () => $request->neverExpire !== null), 'nullable', 'date_format:d/m/Y H:i', Rule::requiredIf(fn () => $request->neverExpire === null) ],
        ]);
        if ( array_key_exists('expiryDate', $validatedData) ) {
            $validatedData['expiryDate'] = Carbon::createFromFormat('d/m/Y H:i', $validatedData['expiryDate']);
        }
        // Create the invite
        $invite = new CourseInvite;
        $invite->is_active = array_key_exists('active', $validatedData);
        $invite->max_uses = array_key_exists('unlimitedUses', $validatedData) ? null : $validatedData['allowedUses'];
        $invite->expiry_date = array_key_exists('neverExpire', $validatedData) ? null : $validatedData['expiryDate'];
        $invite->course_id = $id;
        $invite->invite_id = Str::orderedUuid();
        $invite->id = CourseInvite::all()->count() + 1;
        // Save the invite
        if ( $invite->save() ) {
            return redirect()->to(route('course.settings.get', [ 'id' => $id ]));
        } else {
            return back()->withErrors([ 'SAVE_ERROR' => "Could not save the new invitation." ]);
        }
    }

}
