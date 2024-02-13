<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Course\Course;
use App\Http\Controllers\Course\Invite;
use App\Http\Controllers\Course\CourseFile;
use App\Http\Controllers\File;
use App\Http\Controllers\Question;
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

// Register and login routes
Route::get('/register/', [RegistrationController::class, 'showRegistrationForm']);
Route::post('/register/', [RegistrationController::class, 'registerUser']);

Route::controller(LoginController::class)->group(function () {
   Route::get('/login/', 'showLoginForm')->name('login');
   Route::post('/login', 'login');

   Route::post('/logout/', 'logout')->middleware('auth');
});

// Temporary question page holder
Route::prefix('question')->name('question.')->middleware('auth')->group(function() {
    Route::get('/view/{id}', [Question::class, 'index'])->name('view');
    Route::post('/answer/', [Question::class, 'answer'])->name('answer');
    Route::post('/answer/partial/', [Question::class, 'partial'])->name('partial');
});

// Course pages
Route::prefix('join/')->name('join.')->middleware('auth')->group(function() {
    Route::get('/', [Invite::class, 'show'])->name('show');
    Route::post('/accept', [Invite::class, 'accept'])->name('accept');
});
// An ID is required for all course pages, permissions can be configured in controllers using users and courses
Route::prefix('course/{id}/')->name('course.')->middleware(['auth', 'course'])->group(function() {
    // Home page
    Route::get('/', [Course::class, 'index'])->name('home');
    // Admin-only routes
    Route::middleware('course.owner')->group(function() {
        Route::name('settings.')->group(function() {
            Route::get('settings', [Course::class, 'settings'])->name('get');
            Route::post('settings', [Course::class, 'coreEdit'])->name('set');
        });
        Route::get('settings', [Course::class, 'settings'])->name('settings');
        Route::post('edit', [Course::class, 'contentEdit'])->name('edit');
        Route::get('formrequest', [Course::class, 'formRequest'])->name('getForm');
    });
    // File upload
    Route::name('file.')->group(function() {
        Route::get('all', [CourseFile::class, 'all'])->name('all');
        Route::get('serve/{fileId}', [CourseFile::class, 'serve'])->name('serve');
        Route::get('download/{fileId}', [CourseFile::class, 'download'])->name('download');
        Route::post('upload', [CourseFile::class, 'upload'])->name('upload');
    });
});

// General pages (primarily static content)
Route::get('/', function () {
    return view('welcome');
})->name('home');
