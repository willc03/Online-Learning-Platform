<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    /**
     * This public method facilitates the public GET route 'login', which will present
     * the login form to the user.
     *
     * @return View The view to be send to the user
     */
    public function display ()
    {
        return view('login'); // Present the login view to the user.
    }

    /**
     * This method is designed to validate and process login requests via the POST
     * method. This will ensure the user is properly authenticated against the database
     * and will provide detailed errors if the login request is not successful.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return RedirectResponse The redirect where the user will be sent following authentication attempts
     */
    public function login ( Request $request ) : RedirectResponse
    {
        // Validation is performed on the data before any other processing is carried out
        $validatedData = $request->validate([
            'email'    => [ 'required', 'email' ],
            'password' => [ 'required' ],
            'remember' => [ 'nullable' ],
        ]);
        // Check if the email is found in the database
        if ( !User::where([ 'email' => $validatedData['email'] ])->exists() ) {
            return back()->withErrors([ 'EMAIL_NOT_FOUND' => "The email entered could not be found in our records." ]); // Redirect if the email isn't in the database.
        }
        // Manage remembering users for long periods using 'remember me' box
        if ( $remember = array_key_exists('remember', $validatedData) ) {
            unset($validatedData['remember']); // Remove the entry for the validated data as this will cause conflicts when Auth::attempt is called.
        }
        // Attempt to log in
        if ( Auth::attempt($validatedData, $remember) ) { // This function will automatically authenticate the user if successful.
            $request->session()->regenerate();            // Regenerate the session token for security
            return redirect()->route('home');             // Send the user to the home page (they can view their courses through this)
        }
        // Redirect the user to the home page with a password invalid error
        return redirect()->to('/login/')->withErrors([ 'INVALID_PASSWORD' => 'The password entered does not match our records. Please try again.' ]);
    }

    /**
     * This function is designed to process POST requests to allow the user to end their current
     * authentication session. For security, the session is invalidated and the tokens regenerated.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return RedirectResponse Where the user is sent following the logout process
     */
    public function logout ( Request $request )
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
