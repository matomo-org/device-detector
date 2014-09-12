<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class Library
 *
 * Client parser for tool & software detection
 *
 * @package DeviceDetector\Parser\Client
 */
class Library extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/libraries.yml';
    protected $parserName = 'library';
}