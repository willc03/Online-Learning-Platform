<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Course\Course;
use App\Http\Controllers\Course\CourseFile;
use App\Http\Controllers\Course\Invite;
use App\Http\Controllers\Course\User;
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
Route::get('/register', [RegistrationController::class, 'showRegistrationForm']);
Route::post('/register', [RegistrationController::class, 'registerUser']);

Route::group([], function() {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');
});

// Temporary question page holder
Route::prefix('question')
    ->name('question.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/view/{id}', [Question::class, 'index'])->name('view');
        Route::post('/answer', [Question::class, 'answer'])->name('answer');
        Route::post('/answer/partial', [Question::class, 'partial'])->name('partial');
    });

// Course pages
Route::prefix('join')
    ->name('join.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [Invite::class, 'show'])->name('show');
        Route::post('/accept', [Invite::class, 'accept'])->name('accept');
    });

Route::prefix('course/{id}')
    ->name('course.')
    ->middleware(['auth', 'course'])
    ->group(function () {
        Route::get('/', [Course::class, 'index'])->name('home');

        Route::middleware('course.owner')->group(function () {
            Route::prefix('settings')
                ->name('settings.')
                ->group(function () {
                    Route::get('', [Course::class, 'settings'])->name('get');
                    Route::post('', [Course::class, 'modify'])->name('set');
                    Route::post('invite', [Invite::class, 'modify'])->name('invite');
                    Route::post('invite/new', [Invite::class, 'create'])->name('invite.create');
                    Route::delete('invite', [Invite::class, 'delete'])->name('invite.delete');
                    Route::delete('user/delete', [User::class, 'remove'])->name('user.delete');
                    Route::post('user/block', [User::class, 'block'])->name('user.block');
                });

            Route::post('edit', [Course::class, 'contentEdit'])->name('edit');
            Route::get('formRequest', [Course::class, 'formRequest'])->name('getForm');
        });

        Route::prefix('filestore')
            ->name('file.')
            ->group(function () {
                Route::get('all', [CourseFile::class, 'all'])->name('all');
                Route::get('serve/{fileId}', [CourseFile::class, 'serve'])->name('serve');
                Route::get('download/{fileId}', [CourseFile::class, 'download'])->name('download');
                Route::post('upload', [CourseFile::class, 'upload'])->name('upload')->middleware('course.owner');
                Route::delete('remove', [CourseFile::class, 'remove'])->name('remove')->middleware('course.owner');
            });
    });

// General pages (primarily static content)
Route::get('/', function () {
    return view('welcome');
})->name('home');
