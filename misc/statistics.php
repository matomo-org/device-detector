<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

require_once(__DIR__.'/../vendor/autoload.php');

if(count($argv) != 2) {
    die("invalid arguments. Useage: php statistics.php filetoparse.txt");
}

$parsedUAs = $unknownDeviceTypes =
$detectedBots = 0;

\DeviceDetector\Parser\Device\DeviceParserAbstract::setVersionTruncation(\DeviceDetector\Parser\Device\DeviceParserAbstract::VERSION_TRUNCATION_NONE);

$deviceTypes = (array_fill(0, count(\DeviceDetector\Parser\Device\DeviceParserAbstract::getAvailableDeviceTypes()), 0));

$startTime = microtime(true);

$handle = @fopen($argv[1], "r");

$parser = new \DeviceDetector\DeviceDetector();

if ($handle) {
    while (($line = fgets($handle, 4096)) !== false) {

        if (empty($line)) {
            continue;
        }

        if ($parsedUAs > 0 && $parsedUAs%80 == 0) {
            echo " $parsedUAs\n";
        }

        $parser->setUserAgent(trim($line));
        $parser->parse();

        echo '.';

        $parsedUAs++;

        if ($parser->isBot()) {
            $detectedBots++;
            continue;
        }

        if ($parser->getDevice() !== null) {
            $deviceTypes[$parser->getDevice()]++;
        } else {
            $unknownDeviceTypes++;
        }

    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

$timeElapsed = microtime(true) - $startTime;

function getPercentage($cur, $max) {
    return format(round($cur*100/$max), '   ');
}

function format($str, $length)
{
    return sprintf("%".strlen($length)."d", $str);
}

echo sprintf("

Parsed user agents:            %u

Total time elapsed:            %s
Average time per user agent:   %s

Detected Bots:    %s    (%s%%)

Detected device types:
----------------------------------
Desktop:          %s    (%s%%)
Smartphone:       %s    (%s%%)
Tablet:           %s    (%s%%)
Feature Phone:    %s    (%s%%)
Console:          %s    (%s%%)
TV:               %s    (%s%%)
Car Browser:      %s    (%s%%)
Smart Display:    %s    (%s%%)
Camera:           %s    (%s%%)
Media Player:     %s    (%s%%)
Phablet:          %s    (%s%%)
Unknown:          %s    (%s%%)
----------------------------------
",
$parsedUAs, round($timeElapsed, 2), round($timeElapsed/$parsedUAs, 6), format($detectedBots, $parsedUAs), getPercentage($detectedBots, $parsedUAs),
    format($deviceTypes[0], $parsedUAs), getPercentage($deviceTypes[0], $parsedUAs),
    format($deviceTypes[1], $parsedUAs), getPercentage($deviceTypes[1], $parsedUAs),
    format($deviceTypes[2], $parsedUAs), getPercentage($deviceTypes[2], $parsedUAs),
    format($deviceTypes[3], $parsedUAs), getPercentage($deviceTypes[3], $parsedUAs),
    format($deviceTypes[4], $parsedUAs), getPercentage($deviceTypes[4], $parsedUAs),
    format($deviceTypes[5], $parsedUAs), getPercentage($deviceTypes[5], $parsedUAs),
    format($deviceTypes[6], $parsedUAs), getPercentage($deviceTypes[6], $parsedUAs),
    format($deviceTypes[7], $parsedUAs), getPercentage($deviceTypes[7], $parsedUAs),
    format($deviceTypes[8], $parsedUAs), getPercentage($deviceTypes[8], $parsedUAs),
    format($deviceTypes[9], $parsedUAs), getPercentage($deviceTypes[9], $parsedUAs),
    format($deviceTypes[10], $parsedUAs), getPercentage($deviceTypes[10], $parsedUAs),
    format($unknownDeviceTypes, $parsedUAs), getPercentage($unknownDeviceTypes, $parsedUAs)
);