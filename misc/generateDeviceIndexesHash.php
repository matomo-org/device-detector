<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AliasDevice;

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
    $fileName = pathinfo($fixtureFile, PATHINFO_BASENAME);

    if (in_array($fileName, $excludeFilesNames, false)) {
        continue;
    }

    $fixtures = Spyc::YAMLLoad(file_get_contents($fixtureFile));

    foreach ($fixtures as $fixture) {
        $useragent = $fixture['user_agent'];

        $aliasDevice->setUserAgent($useragent);

        $deviceCode = $aliasDevice->parse()['name'] ?? null;

        if (null === $deviceCode) {
            continue;
        }

        foreach ($deviceDetector->getDeviceParsers() as $parser) {
            $parser->setUserAgent($useragent);
            $results = $parser->parseAllMatch();

            foreach ($results as $result) {
                $brand = $result['brand'] ?? '';

                if (!isset($output[$deviceCode])) {
                    $output[$deviceCode] = [];
                }

                if ('' === $brand || in_array($brand, $output[$deviceCode])) {
                    continue;
                }

                $output[$deviceCode][] = $brand;
            }
        }
    }
}

file_put_contents(__DIR__ . '/../regexes/device-index-hash.yml', Spyc::YAMLDump($output, 2, 0));
