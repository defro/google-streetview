<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\GoogleStreetViewException;
use GuzzleHttp\Client;
use Defro\Google\StreetView\StreetView;

class StreetViewTest extends TestCase
{
    /** @var \Defro\Google\StreetView\StreetView */
    protected $streetView;

    public function setUp()
    {
        parent::setUp();

        $client = new Client();

        $this->streetView = new StreetView($client);

        $apiKey = getenv('GOOGLE_API_KEY');

        if (!$apiKey) {
            $this->markTestSkipped('No Google API key was provided.');

            return;
        }

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
        $result = $this->streetView->getMetadata('A place where I will got an error');
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
