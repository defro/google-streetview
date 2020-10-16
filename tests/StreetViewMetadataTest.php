<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\Api;
use Defro\Google\StreetView\Exception\BadStatusCodeException;
use Defro\Google\StreetView\Exception\RequestException;
use Defro\Google\StreetView\Exception\UnexpectedStatusException;
use Defro\Google\StreetView\Exception\UnexpectedValueException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\StreamHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class StreetViewMetadataTest extends \PHPUnit\Framework\TestCase
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
        $response = new Response(500);
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
            new \GuzzleHttp\Exception\RequestException('', new Request('GET', ''))
        );

        $streetView = new Api($client);

        $this->expectException(RequestException::class);
        $streetView->getMetadata($this->locationName);
    }

    private function getApiWithStatus(string $status)
    {
        $stream = new StreamHandler();
        $stream->status = $status;
        $response = new Response(200, [], json_encode($stream));
        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn($response);

        return new Api($client);
    }

    public function testGetMetadataExceptionZeroResults()
    {
        $streetView = $this->getApiWithStatus('ZERO_RESULTS');

        $this->expectException(UnexpectedStatusException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataExceptionNotFound()
    {
        $streetView = $this->getApiWithStatus('NOT_FOUND');

        $this->expectException(UnexpectedStatusException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataExceptionOverQueryLimit()
    {
        $streetView = $this->getApiWithStatus('OVER_QUERY_LIMIT');

        $this->expectException(UnexpectedStatusException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataExceptionRequestDenied()
    {
        $streetView = $this->getApiWithStatus('REQUEST_DENIED');

        $this->expectException(UnexpectedStatusException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataExceptionInvalidRequest()
    {
        $streetView = $this->getApiWithStatus('INVALID_REQUEST');

        $this->expectException(UnexpectedStatusException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataExceptionUnknownError()
    {
        $streetView = $this->getApiWithStatus('UNKNOWN_ERROR');

        $this->expectException(UnexpectedStatusException::class);
        $streetView->getMetadata($this->locationName);
    }

    public function testGetMetadataExceptionNonExistentStatus()
    {
        $streetView = $this->getApiWithStatus('Non existent status');

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
        $stream->date = 'date';
        $stream->copyright = 'copyright';
        $stream->pano_id = 'panoramaId';
        $response = new Response(200, [], json_encode($stream));

        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn($response);

        $streetView = new Api($client);
        $streetView->setHeading(1);
        $streetView->setSignature('signature');

        $result = $streetView->getMetadata($this->locationName);

        $this->assertArrayHasKey('lat', $result);
        $this->assertArrayHasKey('lng', $result);
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('copyright', $result);
        $this->assertArrayHasKey('panoramaId', $result);
    }
}
