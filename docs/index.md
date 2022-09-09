---
layout: default
---

This library writing in PHP make request easier to make API request to Google Street view API.

# Installation

Use Composer to install this package as a requirement like this :
```bash
composer require defro/google-streetview
```

# Usage

## Initialization
```php
$client = new \GuzzleHttp\Client();
$api = new \Defro\Google\StreetView\Api($client);
$api->setApiKey('YOUR_GOOGLE_API_KEY');
```

## Get image URL by location name
```php
$imgUrl = $api->getImageUrlByLocation('Eiffel tower, Paris');
```

## Get image URL by latitude and longitude
```php
$imgUrl = $api->getImageUrlByLatitudeAndLongitude(48.8557346, 2.2976342);
```

## Get image URL by panorama ID
```php
$imgUrl = $api->getImageUrlByPanoramaId('CAoSLEFGMVFpcFA1SEg3dzFteWloM1JIMy1aZFl3ejBLVFBad0J4UWU0RXRWUGNm');
```

## Get metadata
```php
$metadata = $api->getMetadata('Forbidden City, Beijing, China');
```

# Customize it
A lot of parameters is ready to be overwritten. _This example contains values set by default._
```php
$api
    ->setImageWidth(600)
    ->setImageHeight(600)
    ->setCameraFov(90) // Determines the horizontal field of view of the image expressed in degrees
    ->setCameraPitch(0) // Specifies the up or down angle of the camera relative to the Street View vehicle expressed in degrees
    ->setHeading(0) // Indicates the compass heading of the camera. Accepted values are from 0 to 360
    ->setRadius(50) // Sets a radius, specified in meters, in which to search for a panorama, centered on the given latitude and longitude.
    ->setSource(\Defro\Google\StreetView\Api::SOURCE_OUTDOOR) // Limits Street View searches to selected sources. Valid values are: default or outdoor
;
```

# Run it locally in a Docker container

You can run example script and unit tests in included Docker container, [read how to do it](./docker.md).
