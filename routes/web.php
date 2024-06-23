<?php

use App\Http\Controllers\RequestController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::domain('{bag:slug}.'.config('app.central_domain'))->group(function () {
    Route::any('/{path?}', RequestController::class)
        ->where('path', '.*')
        ->withoutMiddleware(VerifyCsrfToken::class);
});
