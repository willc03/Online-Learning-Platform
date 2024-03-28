<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class Account extends Controller
{

    /**
     * The show route is a basic view that allows the user to view their account page.
     *
     * @return View
     */
    public function show()
    {
        return view("account");
    }

    /**
     * This route processes a POST request that allows the user to change their password.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return RedirectResponse The response that redirects the user based on success or failure of the processing.
     */
    public function change_password(Request $request)
    {
        // Get the user, we can assume it is gettable due to the 'auth' middleware
        $user = $request->user();
        // Validation
        $validatedData = $request->validate([
            'current-password' => ['required'],
            'new-password' => ['required', 'confirmed', Password::defaults()]
        ]);
        // Check the password matches
        if (!Hash::check($validatedData['current-password'], $user->password)) {
            return back()->withErrors(['WRONG_PASSWORD' => 'The password entered does not match our records! Please try again.']);
        }
        // Update the password
        $user->password = Hash::make($validatedData['new-password']);
        // Save the user's new password
        if ($user->save()) {
            return redirect()->to(route('home'))->with(['PASSWORD_CHANGED' => "Your password was successfully updated."]);
        } else {
            return back()->withErrors(['SAVE_ERROR' => 'Your password could not be updated. Please try again later.']);
        }
    }
}
