<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel API with Nova and Sanctum',
        'version' => '1.0.0',
    ]);
});

