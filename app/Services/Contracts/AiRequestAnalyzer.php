<?php

namespace App\Services\Contracts;

use App\Models\Request;

interface AiRequestAnalyzer
{
    public const string PROMPT = <<<'END'
        Analyze raw incoming HTTP request and try to find if it has some errors or something suspicious.
        Take into account the request is awaited to be received as a webhook from a 3rd party service.

    END;

    public function isConfigured(): bool;

    public function analyze(Request $request): ?string;
}
