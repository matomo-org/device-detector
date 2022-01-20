<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

if ('cli' !== php_sapi_name()) {
    die('web not supported');
}

if (2 !== count($argv)) {
    die('Invalid arguments. Usage: php statistics.php filetoparse.txt');
}

require_once(__DIR__ . '/../vendor/autoload.php');

$parsedUAs = $unknownDeviceTypes = $detectedBots = 0;

$deviceAvailableDeviceTypes = array_flip(AbstractDeviceParser::getAvailableDeviceTypes());

AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

$deviceTypes = (array_fill(0, count(AbstractDeviceParser::getAvailableDeviceTypes()), 0));

$startTime = microtime(true);

$handle = @fopen($argv[1], 'r');

$parser = new DeviceDetector();

if ($handle) {
    while (false !== ($line = fgets($handle, 4096))) {
        if (empty($line)) {
            continue;
        }

        if ($parsedUAs > 0 && 0 === $parsedUAs % 80) {
            echo " {$parsedUAs}\n";
        }

        $parser->setUserAgent(trim($line));
        $parser->parse();

        echo '.';

        $parsedUAs++;

        if ($parser->isBot()) {
            $detectedBots++;

            continue;
        }

        if (null !== $parser->getDevice()) {
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

function getPercentage(int $cur, int $max): string
{
    return format((int) round($cur * 100 / $max), '   ');
}

/**
 * @param int|string $str
 * @param int|string $length
 * @return string
 */
function format($str, $length): string
{
    return sprintf('%' . strlen((string) $length) . 'd', $str);
}

$line         = "-------------------------------------------\n";
$mask         = "%-24s %8s %8s \n";
$reportStat   = [];
$reportStat[] = sprintf($mask, 'Type', 'Count', 'Percent');
$reportStat[] = $line;

foreach ($deviceTypes as $deviceTypeId => $deviceCount) {
    $reportStat[] = sprintf(
        $mask,
        sprintf('%s', mb_convert_case($deviceAvailableDeviceTypes[$deviceTypeId], MB_CASE_TITLE)),
        format($deviceCount, $parsedUAs),
        sprintf('(%s%%)', trim(getPercentage($deviceCount, $parsedUAs)))
    );
}

$reportStat[] = sprintf(
    $mask,
    'Unknown',
    format($unknownDeviceTypes, $parsedUAs),
    sprintf('(%s%%)', trim(getPercentage($unknownDeviceTypes, $parsedUAs)))
);
$reportStat[] = $line;

echo sprintf(
    '

Parsed user agents:            %u

Total time elapsed:            %s
Average time per user agent:   %s

Detected Bots:    %s    (%s%%)

Detected device types:
%s
',
    $parsedUAs,
    round($timeElapsed, 2),
    round($timeElapsed / $parsedUAs, 6),
    format($detectedBots, $parsedUAs),
    getPercentage($detectedBots, $parsedUAs),
    implode('', $reportStat)
);
