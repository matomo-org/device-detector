<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser;

use DeviceDetector\ClientHints;

/**
 * Class Bot
 *
 * Parses a user agent for bot information
 *
 * Detected bots are defined in regexes/bots.yml
 */
class Bot extends AbstractBotParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/bots.yml';

    /**
     * @var string
     */
    protected $parserName = 'bot';

    /**
     * @var bool
     */
    protected $discardDetails = false;

    /**
     * Enables information discarding
     */
    public function discardDetails(): void
    {
        $this->discardDetails = true;
    }

    /**
     * Contains a list of mappings from xClient names we use to known x-client values of bots
     *
     * @var array<string, array<string>>
     */
    protected static $xClientMapping = [
        'Collabim' => [
            'name'     => 'Collabim',
            'category' => 'Crawler',
            'url'      => 'https://www.collabim.com/',
        ],
    ];

    /**
     * Parses the current UA and checks whether it contains bot information
     *
     * @see bots.yml for list of detected bots
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in bots.yml and try to match every one
     * -> Return the matched data
     *
     * If $discardDetails is set to TRUE, the Step 2 will be skipped
     * $bot will be set to TRUE instead
     *
     * NOTE: Doing the big match before matching every single regex speeds up the detection
     *
     * @return array|null
     */
    public function parse(): ?array
    {
        if ($this->clientHints instanceof ClientHints && $this->clientHints->getXClient()) {
            foreach (self::$xClientMapping as $key => $result) {
                if ($this->fuzzyCompare($key, $this->clientHints->getXClient())) {
                    if ($this->discardDetails) {
                        return [true];
                    }

                    return $result;
                }
            }
        }

        $result = null;

        if ($this->preMatchOverall()) {
            if ($this->discardDetails) {
                return [true];
            }

            foreach ($this->getRegexes() as $regex) {
                $matches = $this->matchUserAgent($regex['regex']);

                if ($matches) {
                    unset($regex['regex']);
                    $result = $regex;

                    break;
                }
            }
        }

        return $result;
    }
}
