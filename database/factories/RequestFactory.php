<?php

namespace Database\Factories;

use App\Enums\HttpMethod;
use App\Models\Bag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
        return [
            'bag_id' => Bag::factory(),
            'method' => $method = fake()->randomElement([HttpMethod::GET, HttpMethod::POST]),
            'url' => $url = fake()->url()
                .(($qty = fake()->numberBetween(0, 3))
                    ? '?'.Arr::query(array_combine((array) fake()->words($qty), (array) fake()->words($qty)))
                    : ''),
            'headers' => ['host' => [parse_url($url, PHP_URL_HOST)]],
            'post' => $method === HttpMethod::POST
                ? array_combine((array) fake()->words($qty = fake()->numberBetween(0, 3)), (array) fake()->words($qty))
                : [],
            'raw' => '',
            'ips' => [fake()->ipv4()],
        ];
    }
}
