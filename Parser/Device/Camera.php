<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Device;

/**
 * Class Camera
 *
 * Device parser for camera detection
 */
class Camera extends DeviceParserAbstract
{
    protected $fixtureFile = 'regexes/device/cameras.yml';
    protected $parserName  = 'camera';

    public function parse(): ?array
    {
        if (!$this->preMatchOverall()) {
            return null;
        }

        return parent::parse();
    }
}
