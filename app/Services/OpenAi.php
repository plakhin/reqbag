<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI as OpenAiClient;

class OpenAi
{
    public static function request(string $prompt): ?string
    {
        $result = OpenAiClient::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        return $result->choices[0]->message->content;
    }
}
