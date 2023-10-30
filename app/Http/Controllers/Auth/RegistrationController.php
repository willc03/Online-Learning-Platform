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
    /*
     * Create a view to allow users to sign up
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /*
     * Create a function and redirect to create new users
     */
    public function registerUser(Request $request): RedirectResponse
    {
        try {
            // Validate the content contained within the request
            $validated_data = $request->validate([
                'firstname' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Password::defaults()]
            ]);
        } catch (ValidationException $exception) {
            // Redirect if unsuccessful
            return back()->with(['validation_error' => true])->withErrors($exception->errors());
        }
        // Create the new user
        $new_user = User::create([
            'name' => $validated_data['firstname'] . ' ' . $validated_data['lastname'],
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
