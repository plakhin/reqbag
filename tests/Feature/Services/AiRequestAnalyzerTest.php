<?php

declare(strict_types=1);

use App\Services\Contracts\AiRequestAnalyzer;
use OpenAI\Laravel\Facades\OpenAI as OpenAiClient;
use OpenAI\Responses\Chat\CreateResponse;
use Plakhin\RequestChronicle\Models\Request;

test('isConfigured()', function (): void {
    config(['openai.api_key' => null, 'openai.organization' => null]);
    expect(resolve(AiRequestAnalyzer::class)->isConfigured())->toBeFalse();

    config(['openai.api_key' => 'test']);
    expect(resolve(AiRequestAnalyzer::class)->isConfigured())->toBeFalse();

    config(['openai.organization' => 'test']);
    expect(resolve(AiRequestAnalyzer::class)->isConfigured())->toBeTrue();
});

test('analyze()', function (): void {
    OpenAiClient::fake([CreateResponse::fake(['choices' => [['message' => ['content' => 'test']]]])]);

    /** @var Request $request */
    $request = Request::factory()->create();
    $analyzer = resolve(AiRequestAnalyzer::class);

    expect($analyzer::PROMPT)->toBeString()->not()->toBeEmpty();
    expect($analyzer->analyze($request))->toBe('test');
});
