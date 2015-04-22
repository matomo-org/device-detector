<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

use DeviceDetector\Parser\ParserAbstract;

abstract class ClientParserAbstract extends ParserAbstract
{
    protected $fixtureFile = '';
    protected $parserName = '';

    /**
     * Parses the current UA and checks whether it contains any client information
     *
     * @see $fixtureFile for file with list of detected clients
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in feed_readers.yml and try to match every one
     * -> Return the matched feed reader
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
                        'type'       => $this->parserName,
                        'name'       => $this->buildByMatch($regex['name'], $matches),
                        'version'    => $this->buildVersion($regex['version'], $matches)
                    );
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Returns all names defined in the regexes
     *
     * Attention: This method might not return all names of detected clients
     *
     * @return array
     */
    public static function getAvailableClients()
    {
        $instance = new static();
        $regexes = $instance->getRegexes();
        $names = array();
        foreach ($regexes as $regex) {
            if ($regex['name'] != '$1') {
                $names[] = $regex['name'];
            }
        }

        natcasesort($names);

        return array_unique($names);
    }
}
