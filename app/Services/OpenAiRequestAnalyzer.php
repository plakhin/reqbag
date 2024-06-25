<?php

namespace App\Services;

use App\Models\Request;
use App\Services\Contracts\AiRequestAnalyzer;

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
