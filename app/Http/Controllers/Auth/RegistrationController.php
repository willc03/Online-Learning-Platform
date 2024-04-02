<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{

    /**
     * This public route will present the user with the registration form.
     *
     * @return View The view to return to the user
     */
    public function display ()
    {
        return view('static.register');
    }

    /**
     * This function will validate and process registration requests, add these users to the database,
     * and log them in if successful. The Laravel validation methods provide detailed errors if the
     * request is rejected due to validation errors.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return RedirectResponse A response to send the user to the relevant page following validation
     */
    public function create ( Request $request ) : RedirectResponse
    {
        // Validate the content contained within the request
        $validatedData = $request->validate([
            'firstname' => [ 'required', 'string', 'max:255' ],
            'lastname'  => [ 'required', 'string', 'max:255' ],
            'email'     => [ 'required', 'string', 'email', 'max:255', 'unique:' . User::class ],  // Make sure the email hasn't been taken
            'username'  => [ 'required', 'string', 'min:4', 'max:20', 'unique:' . User::class ],            // Make sure the username isn't taken
            'password'  => [ 'required', 'confirmed', Password::defaults() ],                      // Make sure the password conforms to the defined rules & the confirmation of the password is correct
        ]);
        // Convert the email to lower case
        $validatedData['email'] = strtolower($validatedData['email']);
        // Create the new user
        $new_user = User::create([
            'name'     => $validatedData['firstname'] . ' ' . $validatedData['lastname'], // Concatenate the first and last names
            'username' => $validatedData['username'],
            'email'    => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
        // Timestamp the new creation as an event
        event(new Registered($new_user));
        // Log the new user in
        Auth::login($new_user);
        // Redirect the user to the home page
        return redirect()->route('home');
    }

}
