<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\StreetView;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /** @var StreetView */
    protected $streetView;

    protected function setUpMockEnvironment()
    {
        parent::setUp();

        // load environment file
        if (!file_exists(__DIR__.'/../.env')) {
            $this->markTestSkipped('No environment file was provided.');
            return;
        }
        $dotEnv = new Dotenv(__DIR__.'/..');
        $dotEnv->load();

        $apiKey = getenv('GOOGLE_API_KEY');

        if (!$apiKey) {
            $this->markTestSkipped('No Google API key was provided.');
            return;
        }

        $client = new Client();
        $this->streetView = new StreetView($client);
        $this->streetView->setApiKey($apiKey);
    }

}
