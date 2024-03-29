<?php
/**
 * Example script to test Google Street View client.
 *
 * Test it with Dockerfile provided, please read documentation...
 *
 * @category   Example
 *
 * @author     Joël Gaujard <j.gaujard@gmail.com>
 * @copyright  2018 Joël Gaujard
 * @license    MIT https://github.com/defro/google-streetview/blob/master/LICENSE
 *
 * @link       https://defro.github.io/google-streetview/docker.html
 * @see        docs/docker.md
 */

//ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__.'/../vendor/autoload.php';

use Defro\Google\StreetView\Api;

/**
 * You can customize this value to test it.
 */
$imageWidth = 400;
$imageHeight = 400;
$locationName = 'Forbidden City, Beijing, China';
//$locationName = 'A place which not exists';

if (!empty($_GET)) {
    $locationName = !empty($_GET['location_name'])
        ? htmlspecialchars(stripslashes(trim($_GET['location_name'])))
        : $locationName;
}

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
    '<h2>Handle location: "'.$locationName.'"</h2>',
];

try {
    $imgUrl = $streetView->getImageUrlByLocation($locationName);
    $searchUrl = 'https://www.google.com/maps/search/?api=1&query=';
    $searchUrl .= urlencode($locationName);
    $print['Image url by location name'] = [
        '<img src="'.$imgUrl.'" alt="'.$locationName.'" />',
        'Image URL : <a href="'.$imgUrl.'">'.$imgUrl.'</a>',
        'Search URL : <a href="'.$searchUrl.'">'.$searchUrl.'</a>',
    ];
} catch (Exception $e) {
    $print['Image url by location name'] = 'Error when get image url by location name : '.$e->getMessage();
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
    $print['Image url by panorama ID'] = [
        '<img src="'.$imgUrl.'" alt="'.$locationName.'" />',
        'Image URL : <a href="'.$imgUrl.'">'.$imgUrl.'</a>',
        'Search URL : <a href="'.$searchUrl.'">'.$searchUrl.'</a>',
    ];
} catch (Exception $e) {
    $print['Image url by panorama ID'] = 'Error when get image url by panorama ID : '.$e->getMessage();
}

try {
    $imgUrl = $streetView->getImageUrlByLatitudeAndLongitude($metadata['lat'], $metadata['lng']);
    $searchUrl = 'https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=';
    $searchUrl .= $metadata['lat'].','.$metadata['lng'];
    $print['Image url by latitude and longitude'] = [
        '<img src="'.$imgUrl.'" alt="'.$locationName.'" />',
        'Image URL : <a href="'.$imgUrl.'">'.$imgUrl.'</a>',
        'Search URL : <a href="'.$searchUrl.'">'.$searchUrl.'</a>',
    ];
} catch (Exception $e) {
    $print['Image url by latitude and longitude'] = 'Error when get image url by latitude and longitude : '.$e->getMessage();
}

if (PHP_SAPI === 'cli') {
    cliPrinter($print);
} else {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__);
    $twig = new \Twig\Environment($loader);
    echo $twig->render('index.twig', [
        'result'        => webPrinter($print),
        'locationName'  => $locationName,
    ]);
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
