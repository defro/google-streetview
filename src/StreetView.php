<?php

namespace Defro\Google\StreetView;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class StreetView
{
    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var string */
    private $endpointImage = 'https://maps.googleapis.com/maps/api/streetview';

    /** @var string */
    private $endpointMetadata = 'https://maps.googleapis.com/maps/api/streetview/metadata';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $signature;

    /** @var string */
    private $location;

    /** @var float */
    private $latitude;

    /** @var float */
    private $longitude;

    /** @var int */
    private $panoramaId;

    /** @var int */
    private $imageWidth = 600;

    /** @var int */
    private $imageHeight = 600;

    /** @var int */
    private $heading;

    /** @var int */
    private $fov = 90;

    /** @var int */
    private $pitch = 0;

    /** @var int */
    private $radius = 50;

    /** @var string */
    private $source = 'default';


    /**
     * StreetView constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Determines the horizontal field of view of the image.
     * The field of view is expressed in degrees, with a maximum allowed value of 120.
     * When dealing with a fixed-size viewport, as with a Street View image of a set size,
     * field of view in essence represents zoom, with smaller numbers indicating a higher level of zoom.
     *
     * @param int $fov
     * @return $this
     * @throws GoogleStreetViewException
     */
    public function setFov(int $fov):self
    {
        if ($fov > 120) {
            throw new GoogleStreetViewException('FOV value cannot exceed 120 degrees.');
        }

        $this->fov = $fov;

        return $this;
    }

    /**
     * Specifies the up or down angle of the camera relative to the Street View vehicle.
     * This is often, but not always, flat horizontal.
     * Positive values angle the camera up (with 90 degrees indicating straight up);
     * negative values angle the camera down (with -90 indicating straight down).
     *
     * @param int $pitch
     * @return $this
     * @throws GoogleStreetViewException
     */
    public function setPitch(int $pitch):self
    {
        if ($pitch > 90) {
            throw new GoogleStreetViewException(
                'Pitch value for Google Street View cannot exceed 90 degrees.'
            );
        }
        if ($pitch < -90) {
            throw new GoogleStreetViewException(
                'Pitch value for Google Street View cannot be inferior of -90 degrees.'
            );
        }

        $this->pitch = $pitch;

        return $this;
    }

    /**
     * Sets a radius, specified in meters,
     * in which to search for a panorama, centered on the given latitude and longitude.
     * Valid values are non-negative integers.
     *
     * @param int $radius
     * @return $this
     * @throws GoogleStreetViewException
     */
    public function setRadius(int $radius): self
    {
        if ($radius < 0) {
            throw new GoogleStreetViewException(
                'Radius value cannot be negative.'
            );
        }

        $this->radius = $radius;

        return $this;
    }

    /**
     * Indicates the compass heading of the camera.
     * Accepted values are from 0 to 360 (both values indicating North, with 90 indicating East, and 180 South)
     *
     * @param int $heading
     * @return $this
     * @throws GoogleStreetViewException
     */
    public function setHeading(int $heading): self
    {
        if ($heading < 0) {
            throw new GoogleStreetViewException(
                'Heading value cannot be inferior to zero degree.'
            );
        }
        if ($heading > 360) {
            throw new GoogleStreetViewException(
                'Heading value cannot exceed 360 degrees.'
            );
        }

        $this->heading = $heading;

        return $this;
    }

    /**
     * Limits Street View searches to selected sources. Valid values are:
     *  - default uses the default sources for Street View; searches are not limited to specific sources.
     *  - outdoor limits searches to outdoor collections.
     *
     * @param string $source
     * @return $this
     * @throws GoogleStreetViewException
     */
    public function setSource(string $source): self
    {
        if ($source !== 'default' || $source !== 'outdoor') {
            throw new GoogleStreetViewException(
                'Source value is unknown.'
            );
        }

        $this->source = $source;

        return $this;
    }

    /**
     * @param int $height
     * @return $this
     * @throws GoogleStreetViewException
     */
    public function setImageHeight(int $height): self
    {
        if ($height < 1) {
            throw new GoogleStreetViewException(
                'Image height value cannot be negative or equal to zero.'
            );
        }

        $this->imageHeight = $height;

        return $this;
    }

    /**
     * @param int $width
     * @return $this
     * @throws GoogleStreetViewException
     */
    public function setImageWidth(int $width)
    {
        if ($width < 1) {
            throw new GoogleStreetViewException(
                'Image height value cannot be negative or equal to zero.'
            );
        }

        $this->imageWidth = $width;

        return $this;
    }

    /**
     * Digital signature used to verify that any site generating requests
     *
     * @param string $signature
     * @return $this
     */
    public function setSignature(string $signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Returns URL to a static (non-interactive) Street View panorama or thumbnail
     *
     * @param string $address
     * @return string
     * @throws GoogleStreetViewException
     */
    public function getImageUrlByAddress(string $address)
    {
        $parameters = $this->getRequestParameters([
            'location' => $address
        ]);

        return $this->getImageUrl($parameters);
    }

    /**
     * Returns URL to a static (non-interactive) Street View panorama or thumbnail
     *
     * @param float $latitude
     * @param float $longitude
     * @return string
     * @throws GoogleStreetViewException
     */
    public function getImageUrlByLatitudeAndLongitude(float $latitude, float $longitude)
    {
        $parameters = $this->getRequestParameters([
            'location' => $latitude . ', ' . $longitude
        ]);

        return $this->getImageUrl($parameters);
    }

    /**
     * Returns URL to a static (non-interactive) Street View panorama or thumbnail
     *
     * @param string $panoramaId
     * @return string
     * @throws GoogleStreetViewException
     */
    public function getImageUrlByPanoramaId(string $panoramaId)
    {
        $parameters = $this->getRequestParameters([
            'pano' => $panoramaId
        ]);

        return $this->getImageUrl($parameters);
    }

    /**
     * Returns URL to a static (non-interactive) Street View panorama or thumbnail
     * The viewport is defined with URL parameters sent through a standard HTTP request, and is returned as a static image.
     *
     * @param array $parameters
     * @return string
     * @throws GoogleStreetViewException
     */
    private function getImageUrl(array $parameters): string
    {
        $url = $this->endpointImage . '?' . http_build_query($parameters);

        $response = $this->client->get($url);

        if ($response->getStatusCode() !== 200) {
            throw new GoogleStreetViewException('Could not connect to ' . $this->endpointImage);
        }

        return $url;
    }

    /**
     * Requests provide data about Street View panoramas.
     * Using the metadata, you can find out if a Street View image is available at a given location,
     * as well as getting programmatic access to the latitude and longitude,
     * the panorama ID, the date the photo was taken, and the copyright information for the image.
     * Accessing this metadata allows you to customize error behavior in your application.
     * Street View API metadata requests are free to use. No quota is consumed when you request metadata.
     *
     * @param string $location
     * @return array
     * @throws GoogleStreetViewException
     */
    public function getMetadata(string $location): array
    {
        if (empty($location)) {
            throw new GoogleStreetViewException(
                'Location argument cannot be empty to request Google StreetView metadata.'
            );
        }

        $payload = $this->getRequestPayload(compact('location'));

        try {
            $response = $this->client->request('GET', $this->endpointMetadata, $payload);
        }
        catch (GuzzleException $e) {
            throw new GoogleStreetViewException('Guzzle http client request failed.', 0, $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw new GoogleStreetViewException('Could not connect to ' . $this->endpointMetadata);
        }

        $response = json_decode($response->getBody());

        // Indicates that no panorama could be found near the provided location.
        // This may occur if a non-existent or invalid panorama ID is given.
        if ($response->status === 'ZERO_RESULTS') {
            throw new GoogleStreetViewException('Google Street view return zero results.');
        }
        // Indicates that the address string provided in the location parameter could not be found.
        // This may occur if a non-existent address is given.
        if ($response->status === 'NOT_FOUND') {
            throw new GoogleStreetViewException('No Google Street view result found.');
        }
        // Indicates that you have exceeded your daily quota or per-second quota for this API.
        if ($response->status === 'OVER_QUERY_LIMIT') {
            throw new GoogleStreetViewException('Google Street view API quota exceed.');
        }
        // Indicates that your request was denied.
        // This may occur if you did not use an API key or client ID, or
        // if the Street View API is not activated in the Google Cloud Platform Console project containing your API key.
        if ($response->status === 'REQUEST_DENIED') {
            throw new GoogleStreetViewException('Google Street view denied the request.');
        }
        // Generally indicates that the query parameters (address or latlng or components) are missing.
        if ($response->status === 'INVALID_REQUEST') {
            throw new GoogleStreetViewException('Google Street view request is invalid.');
        }
        // Indicates that the request could not be processed due to a server error.
        // This is often a temporary status. The request may succeed if you try again.
        if ($response->status === 'UNKNOWN_ERROR') {
            throw new GoogleStreetViewException('Google Street view unknown error occurred. Please try again.');
        }

        // "OK" 	Indicates that no errors occurred; a panorama is found and metadata is returned.
        return $this->formatMetadataResponse($response);
    }

    protected function formatMetadataResponse($response): array
    {
        return [
            'lat' => $response->location->lat,
            'lng' => $response->location->lng,
            'date' => $response->date,
            'copyright' => $response->copyright,
            'panoramaId' => $response->pano_id
        ];
    }

    private function getRequestPayload(array $parameters): array
    {
        return ['query' => $this->getRequestParameters($parameters)];
    }

    private function getRequestParameters(array $parameters): array
    {
        $defaultParameters = [
            'key' => $this->apiKey,
            'size' => $this->imageWidth . 'x' . $this->imageHeight,
            'fov' => $this->fov,
            'pitch' => $this->pitch,
            'radius' => $this->radius,
            'source' => $this->source
        ];

        // optional parameters which have not default value
        if ($this->heading) {
            $defaultParameters['heading'] = $this->heading;
        }
        if ($this->signature) {
            $defaultParameters['signature'] = $this->signature;
        }

        return array_merge($defaultParameters, $parameters);
    }

}
