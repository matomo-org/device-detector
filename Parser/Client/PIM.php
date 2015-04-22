<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class PIM
 *
 * Client parser for pim (personal information manager) detection
 *
 * @package DeviceDetector\Parser\Client
 */
class PIM extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/pim.yml';
    protected $parserName = 'pim';
}
