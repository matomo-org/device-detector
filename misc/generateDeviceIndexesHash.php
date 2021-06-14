<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\Parser\AliasDevice;
use DeviceDetector\DeviceDetector;

if ('cli' !== php_sapi_name()) {
    echo 'web not supported';
    exit;
}

$aliasDevice               = new AliasDevice();
$deviceDetector            = new DeviceDetector();
$aliasDevice->brandReplace = false;

// get all tests fixtures
$fixtureFiles = array_merge(
    glob(__DIR__ . '/../Tests/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Device/fixtures/*.yml')
);

$excludeFilesNames = ['bots.yml', 'alias_devices.yml'];

$output = [];



foreach ($fixtureFiles as $fixtureFile) {
    $fileName =  pathinfo($fixtureFile, PATHINFO_BASENAME);
    if (in_array($fileName, $excludeFilesNames, false)) {
        continue;
    }
    $fileFixtures = Spyc::YAMLLoad(file_get_contents($file));
    foreach ($fixtureFiles as $fixture) {
        $useragent = $fixture['user_agent'];

        $aliasDevice->setUserAgent($useragent);
        $deviceDetector->setUserAgent($useragent);

        $deviceCode = $aliasDevice->parse()['name'] ?? null;
        if (null === $deviceCode) {
            continue;
        }

        foreach ($deviceDetector->getDeviceParsers() as $parser) {
            $results = $parser->parseAllMatch();
        }
    }

}
