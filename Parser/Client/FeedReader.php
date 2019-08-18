<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Client;

/**
 * Class FeedReader
 *
 * Client parser for feed reader detection
 */
class FeedReader extends AbstractClientParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/client/feed_readers.yml';

    /**
     * @var string
     */
    protected $parserName = 'feed reader';
}
