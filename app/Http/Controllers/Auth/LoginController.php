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
    /**
     * This function will be used to display the login form created as a Blade view.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function display()
    {
        return view('login');
    }

    /**
     * This function is designed to validate and process login requests via the POST
     * method. This will ensure the user is properly authenticated against the database
     * and will provide detailed errors if the login request is not successful.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        // Validation is performed on the data before any other processing is carried out
        try {
            $validated_data = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
                'remember' => ['nullable']
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
        // Manage remembering users for long periods using 'remember me' box
        $remember = array_key_exists('remember', $validated_data);
        if ($remember) {
            unset($validated_data['remember']);
        }
        // Attempt to log in
        if (Auth::attempt($validated_data, $remember)) {
            $request->session()->regenerate(); // Regenerate the session token for security
            return redirect()->route('home');
        }
        // Redirect the user to the home page with a password invalid error
        return redirect()->to('/login/')->withErrors(['password' => 'invalid']);
    }

    /**
     * This function is designed to process POST requests to allow the user to end their current
     * authentication session. For security, the session is invalidated and the tokens regenerated.
     *
     * @param Request $request
     * @return RedirectResponse
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
