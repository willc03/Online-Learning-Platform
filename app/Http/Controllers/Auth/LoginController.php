<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
     * Create a view to allow users to log in
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /*
     * Create a function to log users in and redirect them
     */
    public function login(Request $request): RedirectResponse
    {
        try
        {
            // Validate the content contained within the request
            $validated_data = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);
        } catch (ValidationException)
        {
            // Redirect if unsuccessful
            return back()->with('validation_error', true);
        }

        // Attempt to log in
        if (Auth::attempt($validated_data))
        {
            $request->session()->regenerate(); // Regenerate the session token for security
            return redirect()->route('home');
        }

        // Redirect the user to the home page
        return redirect()->to('/login/');
    }
}
