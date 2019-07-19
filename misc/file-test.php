<?php
require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

if (php_sapi_name() !== 'cli') {
    echo "web not supported";
    exit;
}

if (count($argv) < 2) {
    echo "Usage command:\n";
    echo "php file-test.php <patch to file> <detect mode> <report mode> > report.txt\n\n";

    echo "<detect mode> `detect` - output only detects\n";
    echo "<detect mode> `all` - output all detects\n";
    echo "<detect mode> `not` - output not detects\n\n";

    echo "<report mode> `yml` report yml fixture \n";
    echo "<report mode> `useragent` report useragent string \n\n";
    exit;
}

define('DETECT_MODE_TYPE_DETECT', 'detect');
define('DETECT_MODE_TYPE_ALL', 'all');
define('DETECT_MODE_TYPE_NOT', 'not');

define('REPORT_TYPE_YML', 'yml');
define('REPORT_TYPE_USERAGENT', 'useragent');

if (isset($argv[1])) {
    $file = $argv[1];
}

$showMode = 'not';
if (isset($argv[2])) {
    $showMode = $argv[2];
}

$reportMode = 'yml';
if (isset($argv[3])) {
    $reportMode = $argv[3];
}

/**
 * @param $result
 * @param $format
 */
function printReport($result, $format)
{
    if ($format === REPORT_TYPE_YML) {
        echo Spyc::YAMLDump($result, 2, 0);
        return;
    }
    if ($format === REPORT_TYPE_USERAGENT) {
        echo "{$result['user_agent']}\n";
        return;
    }
}

$fn = fopen($file, "r");
while (!feof($fn)) {
    $userAgent = fgets($fn);
    $userAgent = trim($userAgent);
    DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
    $result = DeviceDetector::getInfoFromUserAgent($userAgent);

    if(!isset($result['device']['model'])){
        continue;
    }

    if ($showMode === DETECT_MODE_TYPE_NOT) {
        if ($result['device']['model'] === '') {
            printReport($result, $reportMode);
        }
    } else if ($showMode === DETECT_MODE_TYPE_DETECT) {
        if ($result['device']['model'] !== '') {
            printReport($result, $reportMode);
        }
    } else if ($showMode === DETECT_MODE_TYPE_ALL) {
        printReport($result, $reportMode);
    }
}

fclose($fn);
