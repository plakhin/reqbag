<?php

declare(strict_types=1);

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI as OpenAiClient;

class OpenAi
{
    public static function request(string $prompt): ?string
    {
        $result = OpenAiClient::chat()->create([
            'model' => config('openai.model', 'gpt-4o-mini'),
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        return $result->choices[0]->message->content;
    }
}
