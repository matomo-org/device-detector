<?php

declare(strict_types=1);

if ('cli' !== php_sapi_name()) {
    echo 'web not supported';
    exit(0);
}

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\Parser\IndexerClient;

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
    glob(__DIR__ . '/../Tests/Parser/Client/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Device/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/fixtures/oss.yml')
);

$browserRegexes = Spyc::YAMLLoad(file_get_contents(__DIR__ . '/../regexes/client/browsers.yml'));
$appRegexes     = Spyc::YAMLLoad(file_get_contents(__DIR__ . '/../regexes/client/mobile_apps.yml'));

foreach ($fixtureFiles as $file) {
    $fileFixtures = Spyc::YAMLLoad(file_get_contents($file));

    echo sprintf('Parse fixture %s', realpath($file)) . PHP_EOL;

    foreach ($fileFixtures as $fixture) {
        $userAgent  = $fixture['user_agent'] ?? null;
        $clientType = $fixture['client']['type'] ?? null;

        if ('' === (string) $userAgent || '' === $clientType) {
            continue;
        }

        $dataIndex = IndexerClient::createDataIndex($userAgent);

        $hash = $dataIndex['hash'] ?? null;

        if ('' === (string) $hash) {
            continue;
        }

        // is hash not exist create empty array

        $index = null;
        $type  = null;

        switch ($clientType) {
            case 'browser':
                $index = findDataIndex($userAgent, $browserRegexes);
                $type  = IndexerClient::BROWSER;

                if (null === $index) {
                    continue 2;
                }

                break;
            case 'mobile app':
                $index = findDataIndex($userAgent, $appRegexes);
                $type  = IndexerClient::APP;

                if (null === $index) {
                    continue 2;
                }

                break;
        }

        if (null === $index) {
            continue;
        }

        if (!isset($output[$hash][$type])) {
            $output[$hash][$type] = [];
        }

        if (in_array($index, $output[$hash][$type], true)) {
            continue;
        }

        $output[$hash][$type][] = $index;
        sort($output[$hash][$type]);
    }
}

if (!empty($output)) {
    file_put_contents(__DIR__ . '/../regexes/client/indexes.yml', Spyc::YAMLDump($output, 2, 0));
}
