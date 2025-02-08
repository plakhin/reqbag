<?php

namespace App\Services;

use App\Services\Contracts\AiRequestAnalyzer;
use Plakhin\RequestChronicle\Models\Request;

class OpenAiRequestAnalyzer implements AiRequestAnalyzer
{
    public function isConfigured(): bool
    {
        return config('openai.api_key') && config('openai.organization');
    }

    public function analyze(Request $request): ?string
    {
        return OpenAi::request(self::PROMPT.preg_replace('/\s+/', ' ', $request->raw));
    }
}
