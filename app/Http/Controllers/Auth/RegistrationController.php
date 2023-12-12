<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /**
     * This function will present the user with the registration form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * This function will validate and process registration requests, add these users to the database,
     * and log them in if successful. The Laravel validation methods provide detailed errors if the
     * request is rejected due to validation errors.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function registerUser(Request $request): RedirectResponse
    {
        try {
            // Validate the content contained within the request
            $validated_data = $request->validate([
                'firstname' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class], // Make sure the email hasn't been taken
                'password' => ['required', 'confirmed', Password::defaults()] // Make sure the password conforms to the defined rules & the confirmation of the password is correct
            ]);
        } catch (ValidationException $exception) {
            // Redirect if unsuccessful
            return back()->with(['validation_error' => true])->withErrors($exception->errors());
        }
        // Convert the email to lower case
        $validated_data['email'] = strtolower($validated_data['email']);
        // Create the new user
        $new_user = User::create([
            'name' => $validated_data['firstname'] . ' ' . $validated_data['lastname'], // Concatenate the first and last names
            'email' => $validated_data['email'],
            'password' => Hash::make($validated_data['password'])
        ]);
        // Timestamp the new creation as an event
        event(new Registered($new_user));
        // Log the new user in
        Auth::login($new_user);
        // Redirect the user to the home page
        return redirect()->route('home');

    }
}
