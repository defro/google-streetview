<?php
/**
 * Test this example by executing:
 * php -S localhost:2626
 * Open your favorite browser and go to http://localhost:2626/example.php
 * OR
 * Print it on stdout using CLI client:
 * $ docker build -t google-street-view . && \
 *     docker run -it --rm --name google-street-view -v "$PWD":/application google-street-view php example/index.php
 *
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);

$vendorDir = getenv('COMPOSER_VENDOR_DIR') ?: 'vendor';
require_once __DIR__.'/../'.$vendorDir.'/autoload.php';

/**
 * You can customize this value to test it.
 */
$imageWidth = 400;
$imageHeight = 400;
$locationName = 'Forbidden City, Beijing, China';
//$locationName = 'A place which not exists';

use Defro\Google\StreetView\Api;

if (file_exists(__DIR__.'/.env')) {
    $dotEnv = new \Dotenv\Dotenv(__DIR__);
    $dotEnv->load();
}

$apiKey = getenv('GOOGLE_API_KEY');
if (!$apiKey || empty($apiKey)) {
    throw new RuntimeException('No Google api key found. Please provide one in .env file.');
}

$client = new \GuzzleHttp\Client();

$streetView = new Api($client);
$streetView
    ->setApiKey($apiKey)
    ->setImageWidth($imageWidth)
    ->setImageHeight($imageHeight);

$print = [
    '<h1>Google Street view example</h1>',
    '<h2>Handle location: "'.$locationName.'"</h2>',
];

try {
    $imgUrl = $streetView->getImageUrlByLocation($locationName);
    $searchUrl = 'https://www.google.com/maps/search/?api=1&query=';
    $searchUrl .= urlencode($locationName);
    $print['Display image url by location name'] = [
        '<img src="'.$imgUrl.'" alt="'.$locationName.'" />',
        'Image URL : <a href="'.$imgUrl.'">'.$imgUrl.'</a>',
        'Search URL : <a href="'.$searchUrl.'">'.$searchUrl.'</a>',
    ];
} catch (Exception $e) {
    $print['Display image url by location name'] = 'Error when get image url by location name : '.$e->getMessage();
}

try {
    $metadata = $streetView->getMetadata($locationName);
    $print['Metadata collect by location name'] = [];
    foreach ($metadata as $key => $value) {
        $print['Metadata collect by location name'][] = sprintf('%s : %s', $key, $value);
    }
} catch (Exception $e) {
    exit('Error when get metadata : '.$e->getMessage());
}

try {
    $imgUrl = $streetView->getImageUrlByPanoramaId($metadata['panoramaId']);
    $searchUrl = 'https://www.google.com/maps/@?api=1&map_action=pano&pano=';
    $searchUrl .= $metadata['panoramaId'];
    $print['Display image url by panorama ID'] = [
        '<img src="'.$imgUrl.'" alt="'.$locationName.'" />',
        'Image URL : <a href="'.$imgUrl.'">'.$imgUrl.'</a>',
        'Search URL : <a href="'.$searchUrl.'">'.$searchUrl.'</a>',
    ];
} catch (Exception $e) {
    $print['Display image url by panorama ID'] = 'Error when get image url by panorama ID : '.$e->getMessage();
}

try {
    $imgUrl = $streetView->getImageUrlByLatitudeAndLongitude($metadata['lat'], $metadata['lng']);
    $searchUrl = 'https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=';
    $searchUrl .= $metadata['lat'].','.$metadata['lng'];
    $print['Display image url by latitude and longitude'] = [
        '<img src="'.$imgUrl.'" alt="'.$locationName.'" />',
        'Image URL : <a href="'.$imgUrl.'">'.$imgUrl.'</a>',
        'Search URL : <a href="'.$searchUrl.'">'.$searchUrl.'</a>',
    ];
} catch (Exception $e) {
    $print['Display image url by latitude and longitude'] = 'Error when get image url by latitude and longitude : '.$e->getMessage();
}

if (PHP_SAPI === 'cli') {
    cliPrinter($print);
} else {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__);
    $twig = new \Twig\Environment($loader);
    echo $twig->render('index.twig', ['result' => webPrinter($print)]);
}

function cliPrinter($print, $indentationLevel = 0)
{
    $indentation = str_repeat(' ', $indentationLevel * 2);
    foreach ($print as $key => $message) {
        if (is_array($message)) {
            printf('%s• %s%s', $indentation, strip_tags($key), PHP_EOL);
            cliPrinter($message, ++$indentationLevel);
            $indentationLevel--;
            continue;
        }
        if (trim(strip_tags($message)) !== '') {
            printf('%s• %s%s', $indentation, strip_tags($message), PHP_EOL);
        }
    }
}

function webPrinter($print, $isList = false)
{
    $return = '';
    foreach ($print as $title => $message) {
        if (is_array($message)) {
            $return .= '<h3>'.$title.'</h3><ul>'.webPrinter($message, true).'</ul>';
            continue;
        }
        $message = $isList ? '<li>'.$message.'</li>' : $message;
        $return .= is_numeric($title) ? $message : '<h3>'.$title.'</h3><p>'.$message.'</p>';
    }

    return $return;
}

exit(0);
