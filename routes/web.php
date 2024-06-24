<?php

use App\Http\Controllers\RequestController;
use App\Providers\Filament\AdminPanelProvider;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

AdminPanelProvider::routes();

Route::domain('{bag:slug}.'.config('app.central_domain'))->group(function () {
    Route::any('/{path?}', RequestController::class)
        ->where('path', '.*')
        ->withoutMiddleware(VerifyCsrfToken::class);
});
