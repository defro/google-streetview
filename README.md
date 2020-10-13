# Google Street View API

[![Latest Version](https://img.shields.io/github/release/defro/google-streetview.svg?style=flat-square)](https://github.com/defro/google-streetview/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/defro/google-streetview/master.svg?style=flat-square)](https://travis-ci.org/defro/google-streetview)
[![SymfonyInsight](https://insight.symfony.com/projects/ddc29c00-efed-47f3-aa44-8b5b086a1d6b/mini.svg)](https://insight.symfony.com/projects/ddc29c00-efed-47f3-aa44-8b5b086a1d6b)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/defro/google-streetview/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/defro/google-streetview/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/defro/google-streetview/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/defro/google-streetview/?branch=master)
[![StyleCI](https://styleci.io/repos/156726302/shield)](https://styleci.io/repos/156726302)
[![Total Downloads](https://img.shields.io/packagist/dt/defro/google-streetview.svg?style=flat-square)](https://packagist.org/packages/defro/google-streetview)

This package can get street view image from any address to GPS coordinates, location or panorama ID using [Google's street view service](https://developers.google.com/maps/documentation/streetview/intro). Here's a quick example:

```php
$client = new \GuzzleHttp\Client();
$streetView = new \Defro\Google\StreetView\Api($client);
$imgUrl = $streetView
    ->setApiKey('YOUR_GOOGLE_API_KEY')
    ->getImageUrlByLocation('Eiffel tower, Paris');

echo '<img src="' . $imgUrl . '" />';
```

## Documentation

Read to install, use this package, customize image to display on [documentation page](https://defro.github.io/google-streetview/).


## License

The MIT License (MIT). Please see [license file](LICENSE) for more information.
