<?php

/**
 * This parser parses the text file
 *
 * The analyzer has 3 settings that allow you to get the desired result.
 * 1 displays only what is detected
 * 2 displays only what is not detected
 * 3 displays any not-detected + detected
 *
 *  source-useragent.txt format file
 *  each useragent on a new line
 * ```
 * Mozilla/5.0 Linux; U; Android 7.0; en-US; K350t Build/K350t AppleWebKit/534.30 KHTML, like Gecko Version/4.0 UCBrowser/11.3.5.972 U3/0.8.0 Mobile Safari/534.30
 * Mozilla/5.0 Linux; U; Android 7.0; en-US; LINX B510 3G LT5037MG Build/NRD90M AppleWebKit/537.36 KHTML, like Gecko Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.11.5.1185 Mobile Safari/537.36
 * Mozilla/5.0 Linux; U; Android 7.0; en-US; K10000 Pro Build/NRD90M AppleWebKit/537.36 KHTML, like Gecko Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.9.7.1158 Mobile Safari/537.36
 * Mozilla/5.0 Linux; U; Android 7.0; en-US; K3 Build/NRD90M AppleWebKit/537.36 KHTML, like Gecko Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.10.2.1164 Mobile Safari/537.36
 * Mozilla/5.0 Linux; U; Android 7.0; en-US; i5532 Build/NRD90M AppleWebKit/534.30 KHTML, like Gecko Version/4.0 UCBrowser/11.1.5.890 U3/0.8.0 Mobile Safari/534.30
 * Mozilla/5.0 Linux; U; Android 7.0; en-US; K900 Build/NRD90M AppleWebKit/537.36 KHTML, like Gecko Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.11.8.1186 Mobile Safari/537.36
 * Mozilla/5.0 Linux; U; Android 7.0; en-US; K5000 Build/NRD90M AppleWebKit/537.36 KHTML, like Gecko Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.0.0.1088 Mobile Safari/537.36
 * ```
 * Example
 *
 * `php file-test.php /tmp/source-useragent.txt "not" "yml" > /tmp/useragent-not-detected.txt`
 * `php file-test.php /tmp/source-useragent.txt "not" "useragent" > /tmp/useragent-not-detected.txt`
 */

declare(strict_types=1);

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

require __DIR__ . '/../vendor/autoload.php';

if ('cli' !== php_sapi_name()) {
    echo 'web not supported';
    exit;
}

if (count($argv) < 2) {
    printHelpAndExit();
}

define('DETECT_MODE_TYPE_DETECT', 'detect');
define('DETECT_MODE_TYPE_ALL', 'all');
define('DETECT_MODE_TYPE_NOT', 'not');

define('REPORT_TYPE_YML', 'yml');
define('REPORT_TYPE_USERAGENT', 'useragent');

$file       = $argv[1] ?? '';
$showMode   = $argv[2] ?? 'not';
$reportMode = $argv[3] ?? 'yml';

function printHelpAndExit(): void
{
    echo "Usage command:\n";
    echo "php file-test.php <patch to file> <detect mode> <report mode> > report.txt\n\n";

    echo "<detect mode> `detect` - displays only what is detected\n";
    echo "<detect mode> `all` - any results not-detected + detected\n";
    echo "<detect mode> `not` - displays only what is not detected\n\n";

    echo "<report mode> `yml` report yml fixture string\n";
    echo "<report mode> `useragent` report useragent string\n\n";
    exit;
}

if (!is_file($file)) {
    echo sprintf("Error: file `%s` not fount\n\n", $file);
    printHelpAndExit();
}

/**
 * @param array $result
 * @param string $format
 */
function printReport(array $result, string $format): void
{
    if (REPORT_TYPE_YML === $format) {
        echo Spyc::YAMLDump($result, 2, 0);

        return;
    }

    if (REPORT_TYPE_USERAGENT === $format) {
        echo "{$result['user_agent']}\n";

        return;
    }
}

AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);
$deviceDetector = new DeviceDetector();

$fn = fopen($file, 'r');

while (!feof($fn)) {
    $userAgent = fgets($fn);

    if (false === $userAgent) {
        break;
    }

    $userAgent = trim($userAgent);

    if (empty($userAgent)) {
        continue;
    }

    $deviceDetector->setUserAgent($userAgent);
    $deviceDetector->parse();

    $result = $deviceDetector->isBot() ? [
        'user_agent' => $deviceDetector->getUserAgent(),
        'bot'        => $deviceDetector->getBot(),
    ] : DeviceDetector::getInfoFromUserAgent($userAgent);

    if (!isset($result['device']['model'])) {
        continue;
    }

    if (DETECT_MODE_TYPE_NOT === $showMode) {
        if ('' === $result['device']['model']) {
            printReport($result, $reportMode);
        }
    } elseif (DETECT_MODE_TYPE_DETECT === $showMode) {
        if ('' !== $result['device']['model']) {
            printReport($result, $reportMode);
        }
    } elseif (DETECT_MODE_TYPE_ALL === $showMode) {
        printReport($result, $reportMode);
    }
}

fclose($fn);
