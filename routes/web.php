<?php

use App\Http\Middleware\SaveRequest;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::domain('{bag}.'.config('app.central_domain'))->group(function () {
    Route::any('/{path?}', fn () => 'ok')
        ->where('path', '.*')
        ->middleware(SaveRequest::class)
        ->withoutMiddleware(VerifyCsrfToken::class);
});
