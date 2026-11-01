<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $connection = \DB::connection();

        if ($connection->getDriverName() === 'sqlite') {
            $pdo = $connection->getPdo();

            $pdo->sqliteCreateFunction('radians', fn ($value) => deg2rad($value));
            $pdo->sqliteCreateFunction('sin', fn ($value) => sin($value));
            $pdo->sqliteCreateFunction('cos', fn ($value) => cos($value));
            $pdo->sqliteCreateFunction('acos', fn ($value) => acos($value));
        }

        // Seed test data into the isolated SQLite test database.
        $this->seed();
    }
}