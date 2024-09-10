<?php

declare(strict_types=1);

if ('cli' !== php_sapi_name()) {
    echo 'web not supported';
    exit(0);
}

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\Parser\Device\AliasDevice;


function findDataIndex(string $userAgent, array $regexes): ?int
{
    foreach ($regexes as $i => $regex) {
        $result = matchUserAgent($userAgent, $regex['regex']);

        if (null !== $result) {
            return $i;
        }
    }

    return null;
}

function matchUserAgent(string $userAgent, string $regex): ?array
{
    $matches = [];

    // only match if useragent begins with given regex or there is no letter before it
    $regex = '/(?:^|[^A-Z0-9_-]|[^A-Z0-9-]_|sprd-|MZ-)(?:' . str_replace('/', '\/', $regex) . ')/i';

    try {
        if (preg_match($regex, $userAgent, $matches)) {
            return $matches;
        }
    } catch (\Exception $exception) {
        throw new \Exception(
            sprintf("%s\nRegex: %s", $exception->getMessage(), $regex),
            $exception->getCode(),
            $exception
        );
    }

    return null;
}

$output = [];

$fixtureFiles = array_merge(
    glob(__DIR__ . '/../Tests/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Device/fixtures/*.yml'),
);

foreach ($fixtureFiles as $file) {
    $fileFixtures = Spyc::YAMLLoad(file_get_contents($file));

    echo sprintf('Parse fixture %s', realpath($file)) . PHP_EOL;

    foreach ($fileFixtures as $fixture) {
        $userAgent  = $fixture['user_agent'] ?? null;
        $brand  = $fixture['device']['brand'] ?? null;

        if ('' === (string) $userAgent || '' === (string)$brand) {
            continue;
        }
    }
}

if (!empty($output)) {
    file_put_contents(__DIR__ . '/../regexes/devices/indexes.yml', Spyc::YAMLDump($output, 2, 0));
}
