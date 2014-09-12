<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class MediaPlayer
 *
 * Client parser for mediaplayer detection
 *
 * @package DeviceDetector\Parser\Client
 */
class MediaPlayer extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/mediaplayers.yml';
    protected $parserName = 'mediaplayer';
}