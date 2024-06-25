<?php

namespace App\Providers;

use App\Services\Contracts\AiRequestAnalyzer;
use App\Services\OpenAiRequestAnalyzer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(AiRequestAnalyzer::class, fn () => new OpenAiRequestAnalyzer);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
    }
}
