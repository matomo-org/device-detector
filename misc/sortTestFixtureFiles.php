<?php

include __DIR__.'/../vendor/autoload.php';

\DeviceDetector\Parser\ParserAbstract::setVersionTruncation(\DeviceDetector\Parser\ParserAbstract::VERSION_TRUNCATION_NONE);

$fixtureFiles = glob(__DIR__.'/../Tests/fixtures/*.yml');

$overwrite = !empty($argv[1]) && $argv[1] === '--f';
$data = array();

foreach ($fixtureFiles AS $file) {

    if (basename($file, '.yml') != 'unknown' && !in_array(preg_replace('/-[0-9]+$/', '', str_replace('_', ' ', basename($file, '.yml'))),array_keys( \DeviceDetector\Parser\Device\DeviceParserAbstract::getAvailableDeviceTypes() ))) {
        continue;
    }

    $fileFixtures = Spyc::YAMLLoad(file_get_contents($file));

    foreach( $fileFixtures AS $fixture) {

        if (isset($fixture['client']['short_name']) && $fixture['client']['short_name'] === true) {
            $fixture['client']['short_name'] = 'ON';
        }

        if (isset($fixture['client']['short_name']) && $fixture['client']['short_name'] === false) {
            $fixture['client']['short_name'] = 'NO';
        }

        if ($overwrite) {
            $fixture = \DeviceDetector\DeviceDetector::getInfoFromUserAgent($fixture['user_agent']);
        }

        $data[$fixture['device']['type']][] = $fixture;

    }
}

foreach( $data AS $deviceType => $fixtures) {

    $fixtures = array_values(array_map("unserialize", array_unique(array_map("serialize", $fixtures))));

    usort($fixtures, function($a, $b) {

        if (empty($b)) {
            return -1;
        }

        if( @$a['device']['brand'] == @$b['device']['brand'] ) {

            if( strtolower(@$a['device']['model']) == strtolower(@$b['device']['model']) ) {

                if (@$a['os']['name'] == @$b['os']['name']) {

                    if (@$a['os']['version'] == @$b['os']['version']) {

                        if (@$a['client']['name'] == @$b['client']['name']) {

                            if (@$a['client']['version'] == @$b['client']['version']) {
                                if ($a['user_agent'] == $b['user_agent']) {
                                    return 0;
                                }

                                return strtolower($a['user_agent']) < strtolower($b['user_agent']) ? -1 : 1;
                            }

                            return strtolower(@$a['client']['version']) < strtolower(@$b['client']['version']) ? -1 : 1;

                        }

                        return strtolower(@$a['client']['name']) < strtolower(@$b['client']['name']) ? -1 : 1;

                    }

                    return strtolower(@$a['os']['version']) < strtolower(@$b['os']['version']) ? -1 : 1;
                }

                return strtolower(@$a['os']['name']) < strtolower(@$b['os']['name']) ? -1 : 1;

            }

            return strtolower(@$a['device']['model']) < strtolower(@$b['device']['model']) ? -1 : 1;

        }

        return @$a['device']['brand'] < @$b['device']['brand'] ? -1 : 1;
    });

    $chunks = array_chunk($fixtures, 500);

    foreach($chunks AS $nr => $chunk) {
        $content = Spyc::YAMLDump($chunk, false, 0);

        $content = str_replace(": ON\n", ": 'ON'\n", $content);
        $content = str_replace(": NO\n", ": 'NO'\n", $content);

        if (empty($deviceType)) {
            $deviceType = 'unknown';
        }

        if ($nr > 0) {
            file_put_contents(
                sprintf(
                    __DIR__ . '/../Tests/fixtures/%s-%s.yml',
                    str_replace(' ', '_', $deviceType),
                    $nr
                ),
                $content
            );
        } else {
            file_put_contents(sprintf(__DIR__ . '/../Tests/fixtures/%s.yml', str_replace(' ', '_', $deviceType)), $content);
        }
    }
}

shell_exec("sed -i -e 's/version: \\([^\"].*\\)/version: \"\\1\"/g' ".__DIR__."/../Tests/fixtures/*.yml");

$botFixtures = Spyc::YAMLLoad(file_get_contents(__DIR__ . '/../Tests/fixtures/bots.yml'));

foreach( $botFixtures AS &$fixture) {
    if ($overwrite) {
        $fixture = \DeviceDetector\DeviceDetector::getInfoFromUserAgent($fixture['user_agent']);
    }
}

usort($botFixtures, function($a, $b) {

    if (empty($b)) {
        return -1;
    }

    if (@$a['bot']['name'] == @$b['bot']['name']) {
        if ($a['user_agent'] == $b['user_agent']) {
            return 0;
        }

        return strtolower($a['user_agent']) < strtolower($b['user_agent']) ? -1 : 1;
    }

    return @$a['bot']['name'] < @$b['bot']['name'] ? -1 : 1;
});

file_put_contents(__DIR__ . '/../Tests/fixtures/bots.yml', Spyc::YAMLDump($botFixtures, false, 0));

echo "done.\n";

