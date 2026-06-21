<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

// rate limit
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/shorten', [UrlController::class, 'store']);
});