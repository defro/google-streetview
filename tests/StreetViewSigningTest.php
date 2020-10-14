<?php

namespace Defro\Google\StreetView\Tests;

use Defro\Google\StreetView\Api;
use GuzzleHttp\Client;

class StreetViewSigningTest extends \PHPUnit\Framework\TestCase
{
    public function testGenerateSignatureFromUrl()
    {
        $streetView = new Api(new Client());
        $streetView->setSigningSecret('aaa');
        $result = $streetView->generateSignature('https://maps.googleapis.com/maps/api/streetview?location=test');
        $this->assertSame($result, 'xkEwlZTZ83QCDJB3dL9yDRK8KRs=');
    }

    public function testGenerateSignatureFromUrlWithParameters()
    {
        $streetView = new Api(new Client());
        $streetView->setSigningSecret('aaa');
        $result = $streetView->generateSignature('https://maps.googleapis.com/maps/api/streetview', [
            'location' => 'test',
        ]);
        $this->assertSame($result, 'xkEwlZTZ83QCDJB3dL9yDRK8KRs=');
    }
}
