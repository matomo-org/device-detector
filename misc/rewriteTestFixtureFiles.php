<?php

declare(strict_types=1);

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';

AbstractParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);

$fixtureFiles = array_merge(
    glob(__DIR__ . '/../Tests/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Client/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Device/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/fixtures/oss.yml')
);

$overwrite = !empty($argv[1]) && '--f' === $argv[1];

foreach ($fixtureFiles as $file) {
    $fileFixtures = Yaml::parse(file_get_contents($file));
    $data         = [];

    foreach ($fileFixtures as $i => $fixture) {
        $keys = array_flip(array_keys($fixture));

        if ($overwrite) {
            $fixture = DeviceDetector::getInfoFromUserAgent($fixture['user_agent']);
        }

        $data[$i] = array_intersect_key($fixture, $keys);
    }

    // fixtures nest at most 5 levels, so nothing is ever dumped inline
    file_put_contents($file, Yaml::dump($data, 10, 2));
}

echo "done.\n";
