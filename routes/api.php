<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Register route: Accepts POST requests for user registration
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Login route: Accepts POST requests for user authentication
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Group routes that require authentication with 'auth:api' middleware
Route::group(['middleware' => ['api', 'auth:api']], function () {

    // Logout route: Accepts POST requests for user logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Logs count route: Accepts GET requests to retrieve the count of logs
    Route::get('/logs/count', [LogController::class, 'countLogs'])->name('logs.count');
});
