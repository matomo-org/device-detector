<?php
require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

if (php_sapi_name() !== 'cli') {
    echo "web not supported";
    exit;
}

if (isset($argv[1])) {
    $file = $argv[1];
}

// detect
// all
// not
$showMode = 'not';
if (isset($argv[2])) {
    $showMode = $argv[2];
}

$fn = fopen($file, "r");
while (!feof($fn)) {
    $userAgent = fgets($fn);
    $userAgent = trim($userAgent);
    DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
    $result = DeviceDetector::getInfoFromUserAgent($userAgent);

    if ($showMode === 'not') {
        if ($result['device']['model'] === '') {
            echo Spyc::YAMLDump($result, 2, 0);
        }
    } else if ($showMode === 'detect') {
        if ($result['device']['model'] !== '') {
            echo Spyc::YAMLDump($result, 2, 0);
        }
    } else {
        echo Spyc::YAMLDump($result, 2, 0);
    }
}

fclose($fn);
