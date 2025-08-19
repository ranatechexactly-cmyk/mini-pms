<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Version 1 Routes
Route::prefix('v1')->name('api.v1.')->group(function () {
    
    // Auth Controller Routes (Public)
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
    });
    
    // Protected Routes (Authentication Required)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth Controller Routes (Protected)
        Route::controller(AuthController::class)->group(function () {
            Route::get('/me', 'me')->name('me');
            Route::post('/logout', 'logout')->name('logout');
        });
    });
    
});