<?php declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

use \DeviceDetector\Parser\Device\AbstractDeviceParser;

if ('cli' !== php_sapi_name()) {
    die('web not supported');
}

$deviceBrands = AbstractDeviceParser::$deviceBrands;
unset($deviceBrands['WB'], $deviceBrands['XX']);

array_multisort($deviceBrands, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE);

$space  = '        ';
$result = '';

foreach ($deviceBrands as $brand) {
    $shortId = (string) array_search($brand, AbstractDeviceParser::$deviceBrands);
    $result .= "{$space}'{$shortId}' => '{$brand}'," . PHP_EOL;
}

$result .= "{$space}// legacy brands, might be removed in future versions" . PHP_EOL;
$result .= "{$space}'WB' => 'Web TV'," . PHP_EOL;
$result .= "{$space}'XX' => 'Unknown'," . PHP_EOL;

$file = __DIR__ . '/../Parser/Device/AbstractDeviceParser.php';

$replace = false;
$fn      = fopen($file, 'r');
$output  = '';

while (!feof($fn)) {
    $line = fgets($fn);

    if (false === $line) {
        continue;
    }

    // check start block
    if (false !== strpos($line, 'public static $deviceBrands') && !$replace) {
        $replace = true;
        $output .= $line;
        $output .= $result;

        continue;
    }

    // check end block
    if ($replace) {
        if (false !== strpos($line, '];')) {
            $output .= $line;
            $replace = false;
        }

        continue;
    }

    $output .= $line;
}

fclose($fn);
file_put_contents($file, $output);
