<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

if ('cli' !== PHP_SAPI) {
    echo 'web not supported';
    exit(0);
}

// create map (max 1296)
$chars = array_merge(
    range('0', '9'),
    range('A', 'Z')
);

$mapIds = [];

foreach ($chars as $first) {
    foreach ($chars as $second) {
        $mapIds[] = $first . $second;
    }
}

$brandExistIds = array_keys(AbstractDeviceParser::$deviceBrands);
$brandFreeIds  = array_diff($mapIds, $brandExistIds);

$browserExistIds = array_keys(Browser::getAvailableBrowsers());
$browserFreeIds  = array_diff($mapIds, $browserExistIds);

function pettyTable(array $data): string
{
    $columns = [];

    foreach ($data as $row) {
        foreach ($row as $cellKey => $cell) {
            $length = strlen($cell);

            if (!empty($columns[$cellKey]) && $columns[$cellKey] < $length) {
                continue;
            }

            $columns[$cellKey] = $length;
        }
    }

    $table = '';

    foreach ($data as $row) {
        foreach ($row as $cellKey => $cell) {
            $table .= str_pad($cell, $columns[$cellKey]) . '   ';
        }

        $table .= PHP_EOL;
    }

    return $table;
}

$chunkSize = 20;

echo sprintf("Free count brands for ids: %s\n", count($brandFreeIds));
echo str_repeat('=====', $chunkSize) . "\n";
echo pettyTable(array_chunk($brandFreeIds, $chunkSize, true));
echo str_repeat('=====', $chunkSize) . "\n";

echo sprintf("Free count browsers for ids: %s\n", count($browserFreeIds));
echo str_repeat('=====', $chunkSize) . "\n";
echo pettyTable(array_chunk($browserFreeIds, $chunkSize, true));
echo str_repeat('=====', $chunkSize) . "\n";
