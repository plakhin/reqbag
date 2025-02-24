<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AiAnalysis;
use App\Services\Contracts\AiRequestAnalyzer;
use App\Services\OpenAiRequestAnalyzer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\ServiceProvider;
use Plakhin\RequestChronicle\Models\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(AiRequestAnalyzer::class, fn (): OpenAiRequestAnalyzer => new OpenAiRequestAnalyzer);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setupModels();
    }

    private function setupModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Request::resolveRelationUsing('analysis', fn (Request $request): HasOne => $request->hasOne(AiAnalysis::class));
    }
}
