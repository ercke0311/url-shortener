<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// rate limit
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/{code}', [UrlController::class, 'redirect']);
});
