<?php

declare(strict_types=1);

pest()
    ->extends(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class)
    ->in('Feature');
