<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

include __DIR__ . '/../vendor/autoload.php';

$brands = \DeviceDetector\Parser\Device\DeviceParserAbstract::$deviceBrands;
natcasesort($brands);

$bots = [];
$ymlParser = new Spyc();

$parsedBots = $ymlParser->loadFile(__DIR__ . '/../regexes/bots.yml');

foreach ($parsedBots as $parsedBot) {
    $bots[] = $parsedBot['name'];
}

natcasesort($bots);

$detections = '## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: '. date('Y/m/d') .'*

### List of detected operating systems:

'. implode(', ', \DeviceDetector\Parser\OperatingSystem::getAvailableOperatingSystems()) .'

### List of detected browsers:

'. implode(', ', \DeviceDetector\Parser\Client\Browser::getAvailableBrowsers()) .'

### List of detected browser engines:

'. implode(', ', \DeviceDetector\Parser\Client\Browser\Engine::getAvailableEngines()) .'

### List of detected libraries:

'. implode(', ', \DeviceDetector\Parser\Client\Library::getAvailableClients()) .'

### List of detected media players:

'. implode(', ', \DeviceDetector\Parser\Client\MediaPlayer::getAvailableClients()) .'

### List of detected mobile apps:

'. implode(', ', \DeviceDetector\Parser\Client\MobileApp::getAvailableClients()) .' and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

'. implode(', ', \DeviceDetector\Parser\Client\PIM::getAvailableClients()) .'

### List of detected feed readers:

'. implode(', ', \DeviceDetector\Parser\Client\FeedReader::getAvailableClients()) .'

### List of brands with detected devices:

'. implode(', ', $brands) .'

### List of detected bots:

'. implode(', ', $bots);

$file = dirname(__FILE__) . '/../README.md';

$readme = file_get_contents($file);

$readme = substr($readme, 0, strpos($readme, '## What Device Detector is able to detect'));

$readme .= $detections;

file_put_contents($file, $readme);