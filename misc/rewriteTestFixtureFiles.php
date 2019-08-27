<?php

include __DIR__.'/../vendor/autoload.php';

\DeviceDetector\Parser\ParserAbstract::setVersionTruncation(\DeviceDetector\Parser\ParserAbstract::VERSION_TRUNCATION_NONE);

$fixtureFiles = array_merge(
    glob(__DIR__.'/../Tests/fixtures/*.yml'),
    glob(__DIR__.'/../Tests/Parser/Client/fixtures/*.yml'),
    glob(__DIR__.'/../Tests/Parser/Devices/fixtures/*.yml')
);

$overwrite = !empty($argv[1]) && $argv[1] === '--f';

foreach ($fixtureFiles AS $file) {

    $fileFixtures = Spyc::YAMLLoad(file_get_contents($file));
    $data         = array();

    foreach( $fileFixtures AS $i => $fixture) {
        $keys = array_flip(array_keys($fixture));

        if (isset($fixture['client']['short_name']) && $fixture['client']['short_name'] === true) {
            $fixture['client']['short_name'] = 'ON';
        }

        if (isset($fixture['client']['short_name']) && $fixture['client']['short_name'] === false) {
            $fixture['client']['short_name'] = 'NO';
        }

        if ($overwrite) {
            $fixture = \DeviceDetector\DeviceDetector::getInfoFromUserAgent($fixture['user_agent']);
        }

        $data[$i] = array_intersect_key($fixture, $keys);

    }

    $content = Spyc::YAMLDump($data, 2, 0);

    $content = str_replace(": ON\n", ": 'ON'\n", $content);
    $content = str_replace(": NO\n", ": 'NO'\n", $content);

    file_put_contents($file, $content);

    shell_exec("sed -i -e 's/version: \\([^\"].*\\)/version: \"\\1\"/g' ".$file);
}

echo "done.\n";

