<?php

namespace Database\Seeders;

use App\Models\AiAnalysis;
use App\Models\Bag;
use Illuminate\Database\Seeder;
use Plakhin\RequestChronicle\Models\Request;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Bag::factory()
            ->test()
            ->has(Request::factory()->count(2)->has(AiAnalysis::factory()->successful(), 'analysis'))
            ->has(Request::factory()->count(2)->has(AiAnalysis::factory()->unsuccessful(), 'analysis'))
            ->has(Request::factory()->count(1)->hasAnalysis())
            ->has(Request::factory()->count(5))
            ->create();
    }
}
