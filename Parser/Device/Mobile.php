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
 * Class Mobile
 *
 * Device parser for mobile detection
 */
class Mobile extends DeviceParserAbstract
{
    protected $fixtureFile = 'regexes/device/mobiles.yml';
    protected $parserName  = 'mobile';
}
