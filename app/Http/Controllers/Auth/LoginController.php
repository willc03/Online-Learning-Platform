<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Validation is performed on the data before any other processing is carried out
        try {
            $validated_data = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);
        } catch (ValidationException) {
            return back()->with('validation_error', true); // Redirect to the original page if validation is unsuccessful
        }
        // Check if the email is found in the database
        try {
            User::where('email', '=', $validated_data['email'])->firstOrFail();
        } catch (ModelNotFoundException) {
            return back()->withErrors(['email' => 'null']); // Redirect if the email isn't in the database
        }
        // Attempt to log in
        if (Auth::attempt($validated_data)) {
            $request->session()->regenerate(); // Regenerate the session token for security
            return redirect()->route('home');
        }
        // Redirect the user to the home page with a password invalid error
        return redirect()->to('/login/')->withErrors(['password' => 'invalid']);
    }

    /*
     * Create a function to allow users to log out
     */
    public function logout(Request $request)
    {
        // Log the user out
        Auth::logout();
        // Regenerate session tokens
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Redirect the user to the home page
        return redirect()->route('home');
    }
}
