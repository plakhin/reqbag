<?php

use App\Http\Middleware\SaveRequest;
use App\Providers\Filament\AdminPanelProvider;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

AdminPanelProvider::routes();

Route::domain('{bag}.'.config('app.central_domain'))->group(function () {
    Route::any('/{path?}', fn () => 'ok')
        ->where('path', '.*')
        ->middleware(SaveRequest::class)
        ->withoutMiddleware(VerifyCsrfToken::class);
});
