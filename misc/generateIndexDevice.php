<?php

declare(strict_types=1);

if ('cli' !== php_sapi_name()) {
    echo 'web not supported';
    exit(0);
}

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\ClientHints;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\Device\AliasDevice;
use Symfony\Component\Yaml\Yaml;

$fixtureFiles = array_merge(
    glob(__DIR__ . '/../Tests/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Device/fixtures/*.yml')
);
$aliasDevice  = new AliasDevice();
$aliasDevice->setReplaceBrand(false);

$iterator      = 0;
$output        = [];
$shorts        = [];
$positions     = [];
$mobileRegexes = Yaml::parseFile(__DIR__ . '/../regexes/device/mobiles.yml');

// get short brands
foreach (AbstractDeviceParser::$deviceBrands as $short => $brand) {
    $shorts[(string) $brand] = (string) $short;
}

// create fixture positions
foreach ($mobileRegexes as $brand => $regex) {
    $short             = $shorts[$brand] ?? null;
    $positions[$short] = $iterator++;
}

// create sort function
$sort = static function (string $a, string $b) use ($positions) {
    return $positions[$a] - $positions[$b];
};

foreach ($fixtureFiles as $file) {
    if (preg_match('~alias-device|bot~i', $file)) {
        continue;
    }

    $fileFixtures = Yaml::parse(file_get_contents($file));

//    echo \sprintf('Parse fixture %s', realpath($file)) . PHP_EOL;

    foreach ($fileFixtures as $fixture) {
        $userAgent = $fixture['user_agent'] ?? null;
        $headers   = $fixture['headers'] ?? [];

        $brand = $fixture['device']['brand'] ?? '';

        if ('' === (string) $brand) {
            echo "unknown brand: {useragent: $userAgent}\n";

            continue;
        }

        $short = $shorts[$brand] ?? '';

        if ('' === (string) $short) {
            echo "unknown short: {brand: $brand, useragent: $userAgent}\n";

            continue;
        }

        $clientHints = [] !== $headers ? ClientHints::factory($headers) : null;

        $aliasDevice->setUserAgent($userAgent);
        $aliasDevice->setClientHints($clientHints);

        $model = $aliasDevice->parse()['name'] ?? '';
        $model = strtolower($model);

        if ('' === $model) {
            echo "not model: {brand: $brand, useragent: $userAgent}\n";

            continue;
        }

        if (!array_key_exists($model, $output)) {
            $output[$model] = [];
        }

        if (in_array((string) $short, $output[$model], true)) {
            continue;
        }

        $output[$model][] = (string) $short;

        if (count($output[$model]) <= 1) {
            continue;
        }

        usort($output[$model], $sort);
    }
}

if (!empty($output)) {
    $content = Yaml::dump($output);
    file_put_contents(__DIR__ . '/../regexes/device/indexes.yml', $content);
}
