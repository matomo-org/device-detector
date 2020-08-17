<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

require_once(__DIR__ . '/../vendor/autoload.php');

if (php_sapi_name() !== 'cli') {
    die("web not supported");
}
if (count($argv) != 2) {
    die("Invalid arguments. Usage: php statistics.php filetoparse.txt");
}

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

$parsedUAs = $unknownDeviceTypes = $detectedBots = 0;

$deviceAvailableDeviceTypes = array_flip(DeviceParserAbstract::getAvailableDeviceTypes());

DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

$deviceTypes = (array_fill(0, count($deviceAvailableDeviceTypes), 0));

$startTime = microtime(true);

$handle = @fopen($argv[1], "r");

$parser = new DeviceDetector();

if ($handle) {
    while (($line = fgets($handle, 4096)) !== false) {

        if (empty($line)) {
            continue;
        }

        if ($parsedUAs > 0 && $parsedUAs % 80 == 0) {
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

function getPercentage($cur, $max)
{
    return format(round($cur * 100 / $max), '   ');
}

function format($str, $length)
{
    return sprintf("%" . strlen($length) . "d", $str);
}

$line = "-------------------------------------------\n";
$mask = "%-24s %8s %8s \n";
$reportStat = [];
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

echo sprintf("

Parsed user agents:            %u

Total time elapsed:            %s
Average time per user agent:   %s

Detected Bots:    %s    (%s%%)

Detected device types:
%s
", $parsedUAs,
    round($timeElapsed, 2),
    round($timeElapsed / $parsedUAs, 6),
    format($detectedBots, $parsedUAs),
    getPercentage($detectedBots, $parsedUAs),
    implode('', $reportStat)
);
