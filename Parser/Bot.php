<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Parser;

/**
 * Class Bot
 *
 * Parses a user agent for bot information
 *
 * Detected bots are defined in regexes/bots.yml
 *
 * @package DeviceDetector\Parser
 */
class Bot extends ParserAbstract
{
    protected $fixtureFile = 'regexes/bots.yml';
    protected $parserName = 'bot';
    protected $discardDetails = false;

    /**
     * Enables information discarding
     */
    public function discardDetails()
    {
        $this->discardDetails = true;
    }

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
     */
    public function parse()
    {
        if (!$this->preMatchOverall()) {
            return null;
        }

        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);
            if ($matches)
                break;
        }

        if (!$matches) {
            return null;
        }

        if ($this->discardDetails) {
            return true;
        }

        unset($regex['regex']);

        return $regex;
    }
}