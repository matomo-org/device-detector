<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser;

use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\Device\AliasDevice;

/**
 * Class IndexerDevice
 * This is an auxiliary class that allows you to find the device brand of regular expression,
 * so that in the future we do not perform a full pass through the array, but a partial one
 */
class IndexerDevice extends AbstractParser
{
    /**
     * @var string
     */
    protected $parserName = 'indexer device';

    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/indexes.yml';

    /**
     * Find brands lists
     *
     * @return array
     */
    public function parse(): array
    {
        if (false === \is_file($this->getRegexesFilePath())) {
            return [];
        }

        $brands      = [];
        $aliasDevice = new AliasDevice();
        $aliasDevice->setReplaceBrand(false);
        $aliasDevice->setUserAgent($this->userAgent);
        $aliasDevice->setClientHints($this->clientHints);
        $model = $aliasDevice->parse()['name'] ?? null;

        if (null !== $model) {
            $model  = \strtolower($model);
            $shorts = $this->getRegexes()[$model] ?? [];

            foreach ($shorts as $short) {
                $brands[] = AbstractDeviceParser::$deviceBrands[$short] ?? '';
            }
        }

        return \array_filter($brands);
    }
}
