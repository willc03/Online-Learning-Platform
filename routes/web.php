<?php

use \App\Http\Controllers\Account;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Course\Course;
use App\Http\Controllers\Course\CourseFile;
use App\Http\Controllers\Course\Invite;
use App\Http\Controllers\Course\Lesson;
use App\Http\Controllers\Course\LessonItem;
use App\Http\Controllers\Course\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
 * These routes will be used to allow the user to confirm their password before
 * completing tasks that require confirmation, such as deleting a course (for
 * which authorisation is required because it would delete all data therein).
 */
Route::get('/confirm-password', function () { // Allow the user to enter their password
    return view('password_confirm');
})->name('password.confirm')->middleware('auth');
Route::post('/confirm-password', function ( Request $request ) { // Process confirmation requests
    $validatedData = $request->validate([
        'password' => [ 'required', 'string' ],
    ]);
    if ( !Hash::check($validatedData['password'], $request->user()->password) ) {
        return back()->withErrors([
            'password' => [ 'The provided password does not match our records.' ],
        ]);
    }
    $request->session()->passwordConfirmed();
    return redirect()->intended();
})->middleware('auth');

// Register and login routes
Route::get('/register', [ RegistrationController::class, 'display' ]);
Route::post('/register', [ RegistrationController::class, 'create' ]);

Route::group([], function () {
    Route::get('/login', [ LoginController::class, 'display' ])->name('login');
    Route::post('/login', [ LoginController::class, 'login' ]);
    Route::post('/logout', [ LoginController::class, 'logout' ])->name('logout')->middleware('auth');
});

/*
 * These routes will be used to allow the user to confirm their password before
 * These routes will be used to allow the user to access course pages, files,
 * and if they are the admin on the course, it will also facilitate CRUD
 * functionality across various aspects of the course.
 */
// Allow users to see all available courses.
Route::get('courses', [ Course::class, 'all' ])->name('courses');
// Allow users to view and request to join courses using invite codes on private courses
Route::prefix('join') // Prefix all URLs with /join
    ->name('join.') // Prefix all route names
    ->middleware('auth') // Make sure the user is logged in
    ->group(function () {
        Route::get('/', [ Invite::class, 'show' ])->name('show'); // Visually display invite results
        Route::post('/accept', [ Invite::class, 'accept' ])->name('accept'); // Process requests to join the course
    });
// Allow users to create their own courses
Route::post('course/new', [ Course::class, 'create' ])->middleware('auth')->name('course.create');
// Allow users to access courses (and edit them if they own it)
//  - Further comments within for specific groups and purposes.
Route::prefix('course/{id}') // {id} mandates an id attribute in the URL
    ->name('course.') // Prefix all route notes
    ->middleware([ 'auth', 'course' ]) // Make sure the user is logged in and they can access the course
    ->group(function () {
        // Course home page
        Route::get('/', [ Course::class, 'index' ])->name('home');
        // Course owner pages
        //  - Allows the owner to access restricted pages and edit aspects of the course
        Route::middleware('course.owner')->group(function () { // Group all owner methods for checks before route access using 'course.owner' middleware
            Route::prefix('settings') // Prefix all URLs with settings, e.g. /course/COURSE_UUID/settings/...
                ->name('settings.') // Prefix all course names
                ->group(function () {
                    Route::get('', [ Course::class, 'settings' ])->name('get'); // View the settings page
                    Route::post('', [ Course::class, 'modify' ])->name('set'); // Modify core aspects of the course (name, description, etc.)
                    Route::post('invite', [ Invite::class, 'modify' ])->name('invite'); // Modify a course invitation
                    Route::post('invite/new', [ Invite::class, 'create' ])->name('invite.create'); // Create a new course invitation
                    Route::delete('invite', [ Invite::class, 'delete' ])->name('invite.delete'); // Delete an existing course invitation
                    Route::delete('user/delete', [ User::class, 'remove' ])->name('user.delete'); // Remove a user from the course
                    Route::post('user/block', [ User::class, 'block' ])->name('user.block'); // Block a user from the course (so they can't rejoin)
                    Route::delete('', [ Course::class, 'delete' ])->name('delete')->middleware('password.confirm'); // Delete the course (with password confirmation)
                });

            Route::post('edit', [ Course::class, 'contentEdit' ])->name('edit'); // Edit the content of the course (sections, components, etc.)
            Route::get('formRequest', [ Course::class, 'formRequest' ])->name('getForm'); // Request certain parts of the page through AJAX (e.g. new section form)
        });
        // Lesson pages
        //  - Lessons are within the course group as they are specific to the course
        Route::prefix('lesson/{lessonId}') // {lessonId} mandates a lesson id in the URL
            ->name('lesson.') // Prefix all route names
            ->group(function () {
                Route::get('', [ Lesson::class, 'display' ])->name('main'); // Display the required content to the user (using sessions for data)
                Route::get('/start', [ Lesson::class, 'start' ])->name('start'); // Set the session details for starting lesson (see controller method)
                Route::post('/answer', [ Lesson::class, 'answer' ])->name('answer'); // Process complete answers through form submits
                Route::post('/partial', [ Lesson::class, 'partial' ])->name('partial'); // Process partial answers using AJAX
                Route::post('/end', [ Lesson::class, 'end' ])->name('end'); // Prematurely end lessons through a request
                // Admin routes
                Route::get('/attempts', [ Lesson::class, 'attempts' ])->name('attempts')->middleware('course.owner'); // Allow course owners to view lesson attempts
                Route::prefix('config')->name('configure.')->group(function () { // Prefix all URLs with /config and prefix all route names
                    Route::get('/', [ Lesson::class, 'config' ])->name('get'); // Show the lesson config page
                    Route::post('/add', [ LessonItem::class, 'create' ])->name('add'); // Process requests to add a component
                    Route::post('/modify', [ Lesson::class, 'modify' ])->name('modify'); // Process requests to change the lesson.
                    Route::get('/form-request', [ Lesson::class, 'formRequest' ])->name('form-request'); // Display certain page aspects to the user (such as question/component forms after type selection)
                })->middleware('course.owner'); // Require the user to be the course owner to access any grouped routes.
            });
        // File store pages
        //  - Files can be accessed/added/deleted using these routes
        Route::prefix('filestore') // Prefix the URL with /filestore
            ->name('file.') // Prefix all route names
            ->group(function () {
                Route::get('serve/{fileId}', [ CourseFile::class, 'serve' ])->name('serve'); // Allow a file to be accessed (this prevents public access to course files)
                Route::get('download/{fileId}', [ CourseFile::class, 'download' ])->name('download'); // Force the browser to accept a payload which downloads a file
                Route::post('upload', [ CourseFile::class, 'upload' ])->name('upload')->middleware('course.owner'); // Allow the course owner to upload files
                Route::delete('remove', [ CourseFile::class, 'remove' ])->name('remove')->middleware('course.owner'); // Allow the user to delete files.
            });
    });
/*
 * These routes will be used to provide the user with the facility
 * to edit their account. At present, the user can only change th-
 * eir password. This will be explained in the report.
 */
Route::prefix("account")
    ->name("account.")
    ->middleware('auth')
    ->group(function() {
        Route::get('/', [ Account::class, 'show' ])->name('show');
        Route::post('/new-password', [Account::class, 'change_password'])->name('new-password');
    });

// General pages (primarily static content)
Route::get('/', function () {
    return view('welcome');
})->name('home');
