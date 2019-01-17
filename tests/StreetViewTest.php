<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\Exception\BadStatusCodeException;
use Defro\Google\StreetView\Exception\RequestException;
use Defro\Google\StreetView\Exception\UnexpectedStatusException;
use Defro\Google\StreetView\Exception\UnexpectedValueException;
use GuzzleHttp\Client;
use Defro\Google\StreetView\Api;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\StreamHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class StreetViewTest extends \PHPUnit\Framework\TestCase
{
    private $locationName = 'Statue of Liberty National Monument';

    public function testGetMetadataStatusUnexpectedValueException()
    {
        $client = $this->createMock(Client::class);
        $streetView = new Api($client);

        $this->expectException(UnexpectedValueException::class);
        $streetView->getMetadata('');
    }

    public function testGetMetadataStatusBadStatusCodeException()
    {
        $response = new Response(0);
        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn($response);

        $streetView = new Api($client);

        $this->expectException(BadStatusCodeException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataStatusRequestException()
    {
        $client = $this->createMock(Client::class);
        $client->method('request')->willThrowException(
            new \GuzzleHttp\Exception\RequestException('', new Request('', ''))
        );

        $streetView = new Api($client);

        $this->expectException(RequestException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataExceptionZeroResults()
    {
        $stream = new StreamHandler();
        $stream->status = 'ZERO_RESULTS';
        $response = new Response(200, [], json_encode($stream));
        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn($response);

        $streetView = new Api($client);

        $this->expectException(UnexpectedStatusException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadata()
    {
        $location = new StreamHandler();
        $location->lat = 'lat';
        $location->lng = 'lng';

        $stream = new StreamHandler();
        $stream->status = 'OK';
        $stream->location = $location;
        //$stream->location->lat = 'lat';
        //$stream->location->lng = 'lng';
        $stream->date = 'date';
        $stream->copyright = 'copyright';
        $stream->pano_id = 'panoramaId';
        $response = new Response(200, [], json_encode($stream));

        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn($response);

        $streetView = new Api($client);

        $result = $streetView->getMetadata($this->locationName);

        $this->assertArrayHasKey('lat', $result);
        $this->assertArrayHasKey('lng', $result);
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('copyright', $result);
        $this->assertArrayHasKey('panoramaId', $result);
    }
/*
    public function testGetImageUrlByLocation()
    {
        $result = $this->streetView->getImageUrlByLocation('Statue of Liberty National Monument');
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
*/
}
