<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\GoogleStreetViewException;
use Defro\Google\StreetView\StreetView;

class StreetViewMockTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->streetView = $this->createMock(StreetView::class);

        $return = [
            'lat'           => 48.8578126,
            'lng'           => 2.2952229,
            'date'          => '2016-05',
            'copyright'     => '© Руслан К',
            'panoramaId'    => 'CAoSLEFGMVFpcE81ZzI0OW9qU3lGeFVkV0kwLVJVZGwyNVl6c2FWVnJOYnJySmt4'
        ];

        $this->streetView
            ->method('getMetadata')
            //->with('Statue of Liberty National Monument')
            ->will($this->onConsecutiveCalls($return, []))
        ;

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
