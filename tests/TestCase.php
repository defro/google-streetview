<?php

namespace Defro\Google\StreetView\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->loadEnvironmentVariables();
    }

    protected function loadEnvironmentVariables(): void
    {
        if (!file_exists(__DIR__.'/../.env')) {
            return;
        }

        $dotEnv = new Dotenv(__DIR__.'/..');

        $dotEnv->load();
    }
}
