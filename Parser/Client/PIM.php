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

    /**
     * Parses the current UA and checks whether it contains PIM information
     *
     * @see pim.yml for list of detected personal information managers
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in pim.yml and try to match every one
     * -> Return the matched pim
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
                        'type'       => 'pim',
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