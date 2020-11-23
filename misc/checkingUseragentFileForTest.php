<?php declare(strict_types=1);

/**
 *  Checking useragent's in the file for the presence of a test
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
 * `php checkingUseragentFileForTest.php /tmp/source-useragent.txt "yml" > /tmp/useragent-not-detected.txt`
 * `php checkingUseragentFileForTest.php /tmp/source-useragent.txt "useragent" > /tmp/useragent-not-detected.txt`
 */

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AliasDevice;
use DeviceDetector\Parser\Bot;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

require __DIR__ . '/../vendor/autoload.php';

if ('cli' !== php_sapi_name()) {
    echo "web not supported\n";
    exit;
}

define('REPORT_TYPE_YML', 'yml');
define('REPORT_TYPE_USERAGENT', 'useragent');

$fixturesPath = \realpath(__DIR__) . '/../Tests/fixtures';

if (!\is_dir($fixturesPath)) {
    echo "test fixtures not exist `{$fixturesPath}'`\n";
    printHelpAndExit();
}

function printHelpAndExit(): void
{
    echo "Usage command:\n";
    echo "php checkingUseragentFileForTest.php <patch to file> <report mode> > report.txt\n\n";
    exit;
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

// generate map exist tests
$existTests = [];

$aliasDevice               = new AliasDevice();
$aliasDevice->brandReplace = false;

$fixtureFiles = \glob(\sprintf('%s/*.yml', $fixturesPath));

foreach ($fixtureFiles as $fixturesPath) {
    $fixtureItems = \Spyc::YAMLLoad($fixturesPath);
    $deviceType   = \str_replace('_', ' ', \substr(\basename($fixturesPath), 0, -4));

    if ('bots' === $deviceType) {
        continue;
    }

    foreach ($fixtureItems as $fixtureItem) {
        $aliasDevice->setUserAgent($fixtureItem['user_agent']);
        $result = $aliasDevice->parse();
        $name   = is_array($result) ? $result['name'] : '';

        if ('' === $name || in_array($name, $existTests)) {
            continue;
        }

        $existTests[] = $name;
    }
}

$file       = $argv[1] ?? '';
$reportMode = $argv[2] ?? REPORT_TYPE_YML;
$existEchos = [];

if ('' === $file && !is_file($file)) {
    echo sprintf("Error: file `%s` not fount\n\n", $file);
    printHelpAndExit();
}

$botDetector = new Bot();
$fn          = fopen($file, 'r');

while (!feof($fn)) {
    $userAgent = fgets($fn);

    if (false === $userAgent) {
        break;
    }

    $userAgent = trim($userAgent);

    if (empty($userAgent)) {
        continue;
    }

    $botDetector->setUserAgent($userAgent);
    $botResult = $botDetector->parse();

    if (!empty($botResult)) {
        continue;
    }

    $aliasDevice->setUserAgent($userAgent);
    $result = $aliasDevice->parse();

    $name = is_array($result) ? $result['name'] : '';

    if ('' === $name || in_array($name, $existTests) || in_array($name, $existEchos)) {
        continue;
    }

    $existEchos[] = $name;
    printReport(DeviceDetector::getInfoFromUserAgent($userAgent), $reportMode);
}
