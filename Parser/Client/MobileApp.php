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

    /**
     * Parses the current UA and checks whether it contains mobile app information
     *
     * @see mobile_apps.yml for list of detected mobile apps
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in mobile_apps.yml and try to match every one
     * -> Return the matched mobile app
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
                        'type'       => 'mobile app',
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