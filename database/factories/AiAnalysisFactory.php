<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Plakhin\RequestChronicle\Models\Request;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiAnalysis>
 */
class AiAnalysisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
        ];
    }

    public function successful(): self
    {
        return $this->state(fn () => ['analysis_result' => [
            'is_successful' => true,
            'response' => fake()->paragraph(),
        ]]);
    }

    public function unsuccessful(): self
    {
        return $this->state(fn () => ['analysis_result' => [
            'is_successful' => false,
            'response' => 'There was an error trying to analyze the request: '.fake()->sentence(),
        ]]);
    }
}
