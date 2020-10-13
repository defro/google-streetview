<?php

// Test this example by executing: php -S localhost:2626
// Open your favorite browser and go to http://localhost:2626/example.php

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once __DIR__.'/vendor/autoload.php';

echo '<h1>Google Street view example</h1>';

$dotEnv = new \Dotenv\Dotenv(__DIR__);
$dotEnv->load();

$apiKey = getenv('GOOGLE_API_KEY');
if (!$apiKey || empty($apiKey)) {
    throw new RuntimeException('No Google api key found.');
}

$client = new \GuzzleHttp\Client();

$streetView = new \Defro\Google\StreetView\Api($client);
$streetView->setApiKey($apiKey);

$locationName = 'Sydney Opera House, Sydney, Australia';
$locationName = 'Forbidden City, Beijing, China';
//$locationName = 'A place which not exists';

echo '<h2>Handle location: "'.$locationName.'"</h2>';

try {
    echo '<h3>Print METADATA collect by location name</h3>';
    $metadata = $streetView->getMetadata($locationName);
    echo '<ol>';
    foreach ($metadata as $key => $value) {
        printf('<li>%s: %s</li>', $key, $value);
    }
    echo '</ol>';
} catch (Exception $e) {
    exit('Error when get metadata : '.$e->getMessage());
}

$streetView->setImageWidth(400)->setImageHeight(400);

try {
    echo '<h3>Display image url by location name</h3>';
    $imgUrl = $streetView->getImageUrlByLocation($locationName);
    printf('<p><a href="https://www.google.com/maps/search/?api=1&query=%s"><img src="%s" alt="%s" /></a></p>', urlencode($locationName), $imgUrl, $locationName);
} catch (Exception $e) {
    exit('Error when get image url by location name : '.$e->getMessage());
}

try {
    echo '<h3>Display image url by panorama ID</h3>';
    $imgUrl = $streetView->getImageUrlByPanoramaId($metadata['panoramaId']);
    printf('<p><a href="https://www.google.com/maps/@?api=1&map_action=pano&pano=%s"><img src="%s" alt="%s" /></a></p>', $metadata['panoramaId'], $imgUrl, $metadata['panoramaId']);
} catch (Exception $e) {
    exit('Error when get image url by panorama ID : '.$e->getMessage());
}

try {
    echo '<h3>Display image url by latitude and longitude</h3>';
    $imgUrl = $streetView->getImageUrlByLatitudeAndLongitude($metadata['lat'], $metadata['lng']);
    printf('<p><a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=%s,%s"><img src="%s" alt="%s" /></a></p>', $metadata['lat'], $metadata['lng'], $imgUrl, $metadata['panoramaId']);
} catch (Exception $e) {
    exit('Error when get image url by latitude and longitude : '.$e->getMessage());
}
