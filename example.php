<?php
# php -S localhost:2626

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

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
//$locationName = 'A place which not exists';

echo '<p>Handle location : ' . $locationName . '</p>';

try {
    $metadata = $streetView->getMetadata($locationName);
    echo '<pre>';print_r($metadata);echo '</pre>';
}
catch (Exception $e) {
    die('Error when get metadata : ' . $e->getMessage());
}

$streetView->setImageWidth(400)->setImageHeight(400);

echo '<ul>';

try {
    $imgUrl = $streetView->getImageUrlByLocation($locationName);
    echo PHP_EOL . '<li>By location name: <img src="' . $imgUrl . '" /></li>';
}
catch (Exception $e) {
    die('Error when get image url by location name : ' . $e->getMessage());
}

try {
    $imgUrl = $streetView->getImageUrlByPanoramaId($metadata['panoramaId']);
    echo PHP_EOL . '<li>By panorama ID: <img src="' . $imgUrl . '" /></li>';
}
catch (Exception $e) {
    die('Error when get image url by panorama ID : ' . $e->getMessage());
}

try {
    $imgUrl = $streetView->getImageUrlByLatitudeAndLongitude($metadata['lat'], $metadata['lng']);
    echo PHP_EOL . '<li>By latitude & longitude: <img src="' . $imgUrl . '" /></li>';
}
catch (Exception $e) {
    die('Error when get image url by latitude and longitude : '.$e->getMessage());
}

echo '</ul>' . PHP_EOL;
