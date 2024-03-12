<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Course\Course;
use App\Http\Controllers\Course\CourseFile;
use App\Http\Controllers\Course\Invite;
use App\Http\Controllers\Course\User;
use App\Http\Controllers\Course\Lesson;
use App\Http\Controllers\Course\LessonItem;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

Route::get('/confirm-password', function () {
    return view('password_confirm');
})->name('password.confirm')->middleware('auth');
Route::post('/confirm-password', function ( Request $request) {
    $validatedData = $request->validate([
        'password' => ['required', 'string']
    ]);
    if (! Hash::check($validatedData['password'], $request->user()->password)) {
        return back()->withErrors([
            'password' => ['The provided password does not match our records.']
        ]);
    }
    $request->session()->passwordConfirmed();
    return redirect()->intended();
})->middleware('auth');

// Register and login routes
Route::get('/register', [RegistrationController::class, 'display']);
Route::post('/register', [RegistrationController::class, 'create']);

Route::group([], function() {
    Route::get('/login', [LoginController::class, 'display'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
});

// Course pages
Route::prefix('join')
    ->name('join.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [Invite::class, 'show'])->name('show');
        Route::post('/accept', [Invite::class, 'accept'])->name('accept');
    });

Route::get('courses', [ Course::class, 'all' ])->name('courses');
Route::post('course/new', [ Course::class, 'create' ])->name('course.create');
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
                    Route::delete('', [ Course::class, 'delete' ])->name('delete')->middleware('password.confirm');
                });

            Route::post('edit', [Course::class, 'contentEdit'])->name('edit');
            Route::get('formRequest', [Course::class, 'formRequest'])->name('getForm');
        });

        Route::prefix('lesson/{lessonId}')
            ->name('lesson.')
            ->group(function () {
                Route::get('', [ Lesson::class, 'display' ])->name('main');
                Route::get('/start', [ Lesson::class, 'start' ])->name('start');
                Route::post('/answer', [ Lesson::class, 'answer' ])->name('answer');
                Route::post('/partial', [ Lesson::class, 'partial' ])->name('partial');
                // Admin routes
                Route::prefix('config')->name('configure.')->group(function () {
                    Route::get('/', [ Lesson::class, 'config' ])->name('get');
                    Route::post('/add', [ LessonItem::class, 'create' ])->name('add');
                    Route::get('/form-request', [ Lesson::class, 'formRequest' ])->name('form-request');
                })->middleware('course.owner');
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
