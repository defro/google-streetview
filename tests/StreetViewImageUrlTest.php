<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\Exception\BadStatusCodeException;
use GuzzleHttp\Client;
use Defro\Google\StreetView\Api;
use GuzzleHttp\Psr7\Response;

class StreetViewImageUrlTest extends \PHPUnit\Framework\TestCase
{
    private function getApi(int $statusCode)
    {
        $response = new Response($statusCode);
        $client = $this->createMock(Client::class);
        $client
            ->method('__call')
            ->with($this->equalTo('get'))
            ->willReturn($response)
        ;

        return new Api($client);
    }

    public function testGetImageUrlBadStatusCodeException()
    {
        $streetView = $this->getApi(0);
        $this->expectException(BadStatusCodeException::class);
        $streetView->getImageUrlByLocation('Location name');
    }

    public function testGetImageUrlByLocation()
    {
        $streetView = $this->getApi(200);
        $result = $streetView->getImageUrlByLocation('Statue of Liberty National Monument');
        $this->assertStringStartsWith('https://', $result);
    }

    public function testGetImageUrlByLatitudeAndLongitude()
    {
        $streetView = $this->getApi(200);
        $result = $streetView->getImageUrlByLatitudeAndLongitude(40.70584913094, -74.035342633881);
        $this->assertStringStartsWith('https://', $result);
    }

    public function testGetImageUrlByPanoramaId()
    {
        $streetView = $this->getApi(200);
        $result = $streetView->getImageUrlByPanoramaId('Bc-tdEJFUCt21hqBjhY_NQ');
        $this->assertStringStartsWith('https://', $result);
    }
}
