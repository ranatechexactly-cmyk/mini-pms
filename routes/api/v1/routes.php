<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
|
| All API routes for version 1 are defined here.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
    });

    // Protected routes (Authentication Required)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::controller(AuthController::class)->group(function () {
            Route::post('/logout', 'logout')->name('logout');
            Route::get('/me', 'me')->name('me');
        });

        // Project routes
        Route::apiResource('projects', ProjectController::class);
        
        // Project developer management routes
        Route::prefix('projects/{project}')->group(function () {
            Route::post('/developers', [ProjectController::class, 'assignDevelopers'])->name('projects.developers.assign');
            Route::delete('/developers/{developerId}', [ProjectController::class, 'removeDeveloper'])->name('projects.developers.remove');
        });

        // Task routes
        Route::apiResource('tasks', TaskController::class);
        
        // Project tasks routes
        Route::get('projects/{project}/tasks', [TaskController::class, 'projectTasks'])->name('projects.tasks.index');
        
        // Task status update route
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status.update');
    });
});
