<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-api', function () {
    return response()->json(['status' => 'ok', 'message' => 'Web route works!']);
});

// Include API routes
Route::prefix('api')->group(base_path('routes/api.php'));
