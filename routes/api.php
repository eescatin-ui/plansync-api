<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReminderController;
use Illuminate\Support\Facades\Route;

// Test route - no auth required
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});
// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - explicitly use api middleware
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/image', [AuthController::class, 'uploadProfileImage']);
    Route::get('/profile/image', [AuthController::class, 'getProfileImage']);
    Route::delete('/profile/image', [AuthController::class, 'removeProfileImage']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
    Route::get('/preferences', [AuthController::class, 'getPreferences']);
    Route::put('/preferences', [AuthController::class, 'savePreferences']);
    
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('notes', NoteController::class);
    Route::apiResource('reminders', ReminderController::class);
});