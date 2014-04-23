<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

if(isset($_GET['ua'])) {
    $userAgent = $_GET['ua'];
} else {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
}

require __DIR__ . '/../vendor/autoload.php';

$result = DeviceDetector::getInfoFromUserAgent($userAgent);

echo '<form><input type="text" name="ua" /><input type="submit" /></form>';

echo "<pre>";

echo Spyc::YAMLDump($result, 2, 0);
echo "</pre>";
