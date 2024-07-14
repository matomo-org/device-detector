<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Client\Browser\Engine;
use DeviceDetector\Parser\Client\FeedReader;
use DeviceDetector\Parser\Client\Library;
use DeviceDetector\Parser\Client\MediaPlayer;
use DeviceDetector\Parser\Client\MobileApp;
use DeviceDetector\Parser\Client\PIM;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\OperatingSystem;

include __DIR__ . '/../vendor/autoload.php';

$brands = AbstractDeviceParser::$deviceBrands;
natcasesort($brands);

$bots      = [];
$ymlParser = new Spyc();

$parsedBots = $ymlParser->loadFile(__DIR__ . '/../regexes/bots.yml');

foreach ($parsedBots as $parsedBot) {
    if (in_array($parsedBot['name'], $bots)) {
        continue;
    }

    $bots[] = $parsedBot['name'];
}

natcasesort($bots);

$detections = '## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: ' . date('Y/m/d') . '*

### List of detected operating systems:

' . implode(', ', OperatingSystem::getAvailableOperatingSystems()) . '

### List of detected browsers:

' . implode(', ', Browser::getAvailableBrowsers()) . '

### List of detected browser engines:

' . implode(', ', Engine::getAvailableEngines()) . '

### List of detected libraries:

' . implode(', ', Library::getAvailableClients()) . '

### List of detected media players:

' . implode(', ', MediaPlayer::getAvailableClients()) . '

### List of detected mobile apps:

' . implode(', ', MobileApp::getAvailableClients()) .
' and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)' .
' or [Electron](https://github.com/electron/electron)*

### List of detected PIMs (personal information manager):

' . implode(', ', PIM::getAvailableClients()) . '

### List of detected feed readers:

' . implode(', ', FeedReader::getAvailableClients()) . '

### List of brands with detected devices:

' . implode(', ', $brands) . '

### List of detected bots:

' . implode(', ', $bots) . "\n";

$file = __DIR__ . '/../README.md';

$readme = file_get_contents($file);

$readme = substr($readme, 0, strpos($readme, '## What Device Detector is able to detect'));

$readme .= $detections;

file_put_contents($file, $readme);
