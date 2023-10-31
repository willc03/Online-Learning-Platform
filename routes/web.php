<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\LoginController;
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

// General pages (primarily static content)
Route::get('/', function () {
    return view('welcome');
})->name('home');
