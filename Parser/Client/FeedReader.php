<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class FeedReader
 *
 * Client parser for feed reader detection
 *
 * @package DeviceDetector\Parser\Client
 */
class FeedReader extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/feed_readers.yml';
    protected $parserName = 'feed reader';
}