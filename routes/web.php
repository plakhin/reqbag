<?php

use App\Models\Bag;
use Illuminate\Support\Facades\Route;
use Plakhin\RequestChronicle\Http\Middleware\SaveRequest;

Route::domain('{bag:slug}.'.config()->string('app.central_domain'))
    ->any('/{path?}', fn (Bag $bag) => 'ok')
    ->where('path', '.*')
    ->middleware(SaveRequest::class.':bag');
