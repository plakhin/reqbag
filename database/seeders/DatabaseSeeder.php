<?php

namespace Database\Seeders;

use App\Models\Bag;
use App\Models\Request;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Bag::factory()
            ->test()
            ->has(Request::factory()->count(10))
            ->create();
    }
}
