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
     * Find brand position
     *
     * @return array|null
     */
    public function parse(): ?array
    {
        return null;
    }
}
