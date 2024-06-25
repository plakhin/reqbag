<?php

use App\Models\Request;
use App\Services\Contracts\AiRequestAnalyzer;
use OpenAI\Laravel\Facades\OpenAI as OpenAiClient;
use OpenAI\Responses\Chat\CreateResponse;

test('isConfigured()', function () {
    config(['openai.api_key' => null, 'openai.organization' => null]);
    expect(resolve(AiRequestAnalyzer::class)->isConfigured())->toBeFalse();

    config(['openai.api_key' => 'test']);
    expect(resolve(AiRequestAnalyzer::class)->isConfigured())->toBeFalse();

    config(['openai.organization' => 'test']);
    expect(resolve(AiRequestAnalyzer::class)->isConfigured())->toBeTrue();
});

test('analyze()', function () {
    OpenAiClient::fake([CreateResponse::fake(['choices' => [['message' => ['content' => 'test']]]])]);

    $request = Request::factory()->make();
    $analyzer = resolve(AiRequestAnalyzer::class);

    expect($analyzer::PROMPT)->toBeString()->not()->toBeEmpty();
    expect($analyzer->analyze($request))->toBe('test');
});
