DeviceDetector
==============

The Universal Device Detection library, that parses User Agents and detects devices (desktop, tablet, mobile, tv, cars, console, etc.), and detects browsers, operating systems, devices, brands and models.

## Usage

```php
$dd = new DeviceDetector($userAgent);

// OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
$dd->discardBotInformation();

// OPTIONAL: If called, getFeedReader() will only return true if a feed reader was detected  (speeds up detection a bit)
$dd->discardFeedReaderInformation()

$dd->parse();

if ($dd->isFeedReader()) {
  // handle feed readers
  $feedReaderInfo = $dd->getFeedReader();
} else if ($dd->isBot()) {
  // handle bots,spiders,crawlers,...
  $botInfo = $dd->getBot();
} else {
  $browserInfo = $dd->getBrowser();
  $osInfo = $dd->getOs();
  $device = $dd->getDevice();
  $brand = $dd->getBrand();
  $model = $dd->getModel();
}
```

## Tests

Build status (master branch) [![Build Status](https://travis-ci.org/piwik/device-detector.png?branch=master)](https://travis-ci.org/piwik/device-detector)

See also: [QA at Piwik](http://piwik.org/qa/)

## Contributors

Created by the [Piwik team](http://piwik.org/team/), Stefan Giehl, Matthieu Aubry, Michał Gaździk, 
Tomasz Majczak, Grzegorz Kaszuba, Piotr Banaszczyk and contributors.

Together we can build the best Device Detection library. 

We are looking forward to your contributions and pull requests!
