<?php

namespace Database\Factories;

use App\Enums\HttpMethod;
use App\Models\Bag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bag = Bag::factory()->make();

        $method = fake()->randomElement([HttpMethod::GET, HttpMethod::POST]); /** @var HttpMethod $method */
        $host = $bag->slug.'.'.config('app.central_domain');

        $url = ($qty = fake()->numberBetween(0, 3))
            ? '?'.Arr::query(array_combine((array) fake()->words($qty), (array) fake()->words($qty)))
            : '';

        $userAgent = fake()->userAgent();

        $headers = [
            'accept' => ['*/*'],
            'user-agent' => [$userAgent],
            'host' => [$host],
        ];

        $payload = $method === HttpMethod::POST
            ? array_combine((array) fake()->words($qty = fake()->numberBetween(0, 3)), (array) fake()->words($qty))
            : [];

        $raw = str("{$method->name} /{$url} HTTP/2.0\n")
            ->append("Accept:         */*\n")
            ->append("Host:           {$host}\n")
            ->append("User-Agent:     {$userAgent}\n")
            ->when(
                count($payload),
                fn (Stringable $raw) => $raw
                    ->append('Content-Length: '.strlen((string) json_encode($payload))."\n")
                    ->append("Content-Type:   application/json\n\n")
                    ->append((string) json_encode($payload))
            )->toString();

        $ip = fake()->ipv4();

        return [
            'bag_id' => $bag,
            'method' => $method,
            'url' => config('app.scheme').$host.$url,
            'headers' => $headers,
            'payload' => $payload,
            'raw' => $raw,
            'ips' => [$ip],
        ];
    }
}
