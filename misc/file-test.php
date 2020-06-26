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
require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
use DeviceDetector\Parser\OperatingSystem;

if (php_sapi_name() !== 'cli') {
    echo "web not supported";
    exit;
}

if (count($argv) < 2) {
    echo "Usage command:\n";
    echo "php file-test.php <patch to file> <detect mode> <report mode> > report.txt\n\n";

    echo "<detect mode> `detect` - displays only what is detected\n";
    echo "<detect mode> `all` - any results not-detected + detected\n";
    echo "<detect mode> `not` - displays only what is not detected\n\n";

    echo "<report mode> `yml` report yml fixture string\n";
    echo "<report mode> `useragent` report useragent string\n\n";
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

DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
$deviceDetector = new DeviceDetector();

$fn = fopen($file, "r");
while (!feof($fn)) {
    $userAgent = fgets($fn);
    $userAgent = trim($userAgent);

    if (empty($userAgent)) {
        continue;
    }

    $deviceDetector->setUserAgent($userAgent);
    $deviceDetector->parse();

    if ($deviceDetector->isBot()) {
        $result = array(
            'user_agent' => $deviceDetector->getUserAgent(),
            'bot' => $deviceDetector->getBot()
        );
    } else {
        $osFamily = OperatingSystem::getOsFamily($deviceDetector->getOs('short_name'));
        $browserFamily = Browser::getBrowserFamily($deviceDetector->getClient('short_name'));
        $result = array(
            'user_agent' => $deviceDetector->getUserAgent(),
            'os' => $deviceDetector->getOs(),
            'client' => $deviceDetector->getClient(),
            'device' => array(
                'type' => $deviceDetector->getDeviceName(),
                'brand' => $deviceDetector->getBrand(),
                'model' => $deviceDetector->getModel(),
            ),
            'os_family' => $osFamily !== false ? $osFamily : 'Unknown',
            'browser_family' => $browserFamily !== false ? $browserFamily : 'Unknown',
        );
    }

    if (!isset($result['device']['model'])) {
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
