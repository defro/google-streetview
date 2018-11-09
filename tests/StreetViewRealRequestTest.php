<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\GoogleStreetViewException;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Defro\Google\StreetView\StreetView;

class StreetViewRealRequestTest extends TestCase
{
    public function setUp()
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


    public function testGetMetadata()
    {
        $result = $this->streetView->getMetadata('Statue of Liberty National Monument');

        $this->assertArrayHasKey('lat', $result);
        $this->assertArrayHasKey('lng', $result);
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('copyright', $result);
        $this->assertArrayHasKey('panoramaId', $result);
    }

    public function testGetMetadataExceptions()
    {
        $this->expectException(GoogleStreetViewException::class);
        $this->streetView->getMetadata('A place where I will got an error');
    }

    public function testGetImageUrlByAddress()
    {
        $result = $this->streetView->getImageUrlByAddress('Statue of Liberty National Monument');
        $this->assertStringStartsWith('https://', $result);
    }

    public function testGetImageUrlByLatitudeAndLongitude()
    {
        $result = $this->streetView->getImageUrlByLatitudeAndLongitude(40.70584913094, -74.035342633881);
        $this->assertStringStartsWith('https://', $result);
    }

    public function testGetImageUrlByPanoramaId()
    {
        $result = $this->streetView->getImageUrlByPanoramaId('Bc-tdEJFUCt21hqBjhY_NQ');
        $this->assertStringStartsWith('https://', $result);
    }

}
