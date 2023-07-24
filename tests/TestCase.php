<?php

namespace Tests;

use App\Models\Configuration;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');
        $configurations = Configuration::all();
        config()->set('app.system', $configurations);
    }

    protected function tearDown(): void
    {
        $this->artisan('migrate:reset');
        parent::tearDown();
    }
}
