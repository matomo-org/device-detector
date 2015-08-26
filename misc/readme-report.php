<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

include __DIR__ . '/../vendor/autoload.php';

?>
## What Device Detector is able to detect

The lists below are auto generated and updated from time to time. Some of them might not be complete.

*Last update: <?php echo date('Y/m/d'); ?>*

### List of detected operating systems:

<?php echo implode(', ', \DeviceDetector\Parser\OperatingSystem::getAvailableOperatingSystems()); ?>


### List of detected browsers:

<?php echo implode(', ', \DeviceDetector\Parser\Client\Browser::getAvailableBrowsers()); ?>


### List of detected browser engines:

<?php echo implode(', ', \DeviceDetector\Parser\Client\Browser\Engine::getAvailableEngines()); ?>


### List of detected libraries:

<?php echo implode(', ', \DeviceDetector\Parser\Client\Library::getAvailableClients()); ?>


### List of detected media players:

<?php echo implode(', ', \DeviceDetector\Parser\Client\MediaPlayer::getAvailableClients()); ?>


### List of detected mobile apps:

<?php echo implode(', ', \DeviceDetector\Parser\Client\MobileApp::getAvailableClients()); ?>  and *mobile apps using [AFNetworking](https://github.com/AFNetworking/AFNetworking)*

### List of detected PIMs (personal information manager):

<?php echo implode(', ', \DeviceDetector\Parser\Client\PIM::getAvailableClients()); ?>


### List of detected feed readers:

<?php echo implode(', ', \DeviceDetector\Parser\Client\FeedReader::getAvailableClients()); ?>


### List of brands with detected devices:

<?php echo implode(', ', \DeviceDetector\Parser\Device\DeviceParserAbstract::$deviceBrands); ?>

