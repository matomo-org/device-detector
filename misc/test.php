<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

if ('cli' === php_sapi_name()) {
    if (isset($argv[1])) {
        $userAgent = $argv[1];
    }
} else {
    $userAgent = $_GET['ua'] ?? $_SERVER['HTTP_USER_AGENT'];
}

AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

$result = DeviceDetector::getInfoFromUserAgent($userAgent);

if ('cli' === php_sapi_name()) {
    echo Spyc::YAMLDump($result, 2, 0);
    exit;
}

echo '<form><input type="text" name="ua" /><input type="submit" /></form>';

echo '<pre>';

echo Spyc::YAMLDump($result, 2, 0);

echo '</pre>';
