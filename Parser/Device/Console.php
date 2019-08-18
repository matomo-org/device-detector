<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Device;

/**
 * Class Console
 *
 * Device parser for console detection
 */
class Console extends AbstractDeviceParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/consoles.yml';

    /**
     * @var string
     */
    protected $parserName = 'console';

    /**
     * @inheritdoc
     */
    public function parse(): ?array
    {
        if (!$this->preMatchOverall()) {
            return null;
        }

        return parent::parse();
    }
}
