<?php

namespace App\Services\Contracts;

use App\Models\Request;

interface AiRequestAnalyzer
{
    public const string PROMPT = "Analyze raw HTTP request and try to find if it has some errors or something suspicious:\n";

    public function isConfigured(): bool;

    public function analyze(Request $request): ?string;
}
