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
    protected $parserName = 'media player';

    /**
     * Parses the current UA and checks whether it contains mediaplayer information
     *
     * @see mediaplayers.yml for list of detected bots
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in mediaplayers.yml and try to match every one
     * -> Return the matched mediaplayer
     *
     * NOTE: Doing the big match before matching every single regex speeds up the detection
     */
    public function parse()
    {
        $result = null;

        if ($this->preMatchOverall()) {

            foreach ($this->getRegexes() as $regex) {

                $matches = $this->matchUserAgent($regex['regex']);

                if ($matches) {

                    $result = array(
                        'type'       => 'mediaplayer',
                        'name'       => $this->buildByMatch($regex['name'], $matches),
                        'version'    => $this->buildVersion($regex['version'], $matches)
                    );
                    break;
                }
            }
        }

        return $result;
    }
}