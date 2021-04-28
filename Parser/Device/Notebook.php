<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Device;

/**
 * Class Notebook
 *
 * Device parser for notebook detection in Facebook useragents
 */
class Notebook extends AbstractDeviceParser
{
    protected $fixtureFile = 'regexes/device/notebooks.yml';

    protected $parserName = 'notebook';

    /**
     * @inheritdoc
     */
    public function parse(): ?array
    {
        if (!$this->matchUserAgent('FBMD/')) {
            return null;
        }

        return parent::parse();
    }
}
