<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class MobileApp
 *
 * Client parser for mobile app detection
 *
 * @package DeviceDetector\Parser\Client
 */
class MobileApp extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/mobile_apps.yml';
    protected $parserName = 'mobile app';
}