<?php declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

\DeviceDetector\Parser\AbstractParser::setVersionTruncation(\DeviceDetector\Parser\AbstractParser::VERSION_TRUNCATION_NONE);

$fixtureFiles = array_merge(
    glob(__DIR__ . '/../Tests/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Client/fixtures/*.yml'),
    glob(__DIR__ . '/../Tests/Parser/Device/fixtures/*.yml')
);

$overwrite = !empty($argv[1]) && '--f' === $argv[1];

foreach ($fixtureFiles as $file) {
    $fileFixtures = Spyc::YAMLLoad(file_get_contents($file));
    $data         = [];

    foreach ($fileFixtures as $i => $fixture) {
        $keys = array_flip(array_keys($fixture));

        if ($overwrite) {
            $fixture = \DeviceDetector\DeviceDetector::getInfoFromUserAgent($fixture['user_agent']);
        }

        $data[$i] = array_intersect_key($fixture, $keys);
    }

    $content = Spyc::YAMLDump($data, 2, 0);

    $content = str_replace(": ON\n", ": 'ON'\n", $content);
    $content = str_replace(": NO\n", ": 'NO'\n", $content);

    file_put_contents($file, $content);

    shell_exec("sed -i -e 's/version: \\([^\"].*\\)/version: \"\\1\"/g' " . $file);
}

echo "done.\n";
