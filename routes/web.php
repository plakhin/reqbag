<?php

declare(strict_types=1);

use App\Models\Bag;
use Illuminate\Support\Facades\Route;
use Plakhin\RequestChronicle\Http\Middleware\SaveRequest;

Route::domain('{bag:slug}.'.config()->string('app.central_domain'))
    ->any('/{path?}', fn (Bag $bag): string => 'ok')
    ->where('path', '.*')
    ->middleware(SaveRequest::class.':bag');
