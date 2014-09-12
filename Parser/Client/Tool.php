<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class Tool
 *
 * Client parser for tool & software detection
 *
 * @package DeviceDetector\Parser\Client
 */
class Tool extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/tools.yml';
    protected $parserName = 'tool';
}