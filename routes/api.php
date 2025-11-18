<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Example API routes for SPA
Route::prefix('v1')->group(function () {
    // Add your API routes here
    Route::get('/health', function () {
        return response()->json(['status' => 'ok']);
    });
});

