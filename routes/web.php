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

Route::get('/login/', [LoginController::class, 'showLoginForm']);
Route::post('/login/', [LoginController::class, 'login']);

Route::post('/logout/', [LoginController::class, 'logout']);

// General pages (primarily static content)
Route::get('/', function () {
    return view('welcome');
})->name('home');
