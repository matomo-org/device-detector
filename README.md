DeviceDetector
==============

The Universal Device Detection library, that parses User Agents and detects devices (desktop, tablet, mobile, tv, cars, console, etc.), and detects browsers, operating systems, devices, brands and models.

## Usage

```
$dd = new DeviceDetector($userAgent);
$dd->parse();
$browserInfo = $dd->getBrowser();
$osInfo = $dd->getOs();
$device = $dd->getDevice();
$brand = $dd->getBrand();
$model = $dd->getModel();
```

## Tests

Build status (master branch) [![Build Status](https://travis-ci.org/piwik/device-detector.png?branch=master)](https://travis-ci.org/piwik/device-detector)

See also: [QA at Piwik](http://piwik.org/qa/)

## Contributors

Created by the [Piwik team](http://piwik.org/team/), Michał Gaździk, and contributors.

Together we can build the best Device Detection library. 

We are looking forward to your contributions and pull requests!
