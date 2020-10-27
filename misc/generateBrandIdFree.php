<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DeviceDetector\Parser\Device\AbstractDeviceParser;

if ('cli' !== php_sapi_name()) {
    echo 'web not supported';
    exit;
}

$mapIds = [];
// create map (max 1296)
$c = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

for ($n = 65; $n < 123; $n++) {
    $n < 91 || $n > 96 and $c[] = chr($n);
}

for ($y = 0; $y < 36; $y++) {
    for ($x = 0; $x < 36; $x++) {
        $mapIds[] = sprintf('%s%s', $c[$x], $c[$y]);
    }
}

$brandExistIds = array_keys(AbstractDeviceParser::$deviceBrands);
$brandFreeIds  = array_diff($mapIds, $brandExistIds);

function pettyTable(array $data): string
{
    $columns = [];

    foreach ($data as $rowKey => $row) {
        foreach ($row as $cellKey => $cell) {
            $length = strlen($cell);

            if (!empty($columns[$cellKey]) && $columns[$cellKey] < $length) {
                continue;
            }

            $columns[$cellKey] = $length;
        }
    }

    $table = '';

    foreach ($data as $rowKey => $row) {
        foreach ($row as $cellKey => $cell) {
            $table .= str_pad($cell, $columns[$cellKey]) . '   ';
        }

        $table .= PHP_EOL;
    }

    return $table;
}

$chunkSize = 20;
echo sprintf("Free count ids: %s\n", count($brandFreeIds));
echo str_repeat('=====', $chunkSize) . "\n";
echo pettyTable(array_chunk($brandFreeIds, $chunkSize, true));
echo str_repeat('=====', $chunkSize) . "\n";
