<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser;

use DeviceDetector\Parser\Device\DeviceParserAbstract;

/**
 * Class VendorFragments
 *
 * Device parser for vendor fragment detection
 *
 * @package DeviceDetector\Parser\Device
 */
class VendorFragment extends ParserAbstract
{
    protected $fixtureFile = 'regexes/vendorfragments.yml';
    protected $parserName  = 'vendorfragments';

    protected $matchedRegex = null;

    public function parse()
    {
        foreach ($this->getRegexes() as $brand => $regexes) {
            foreach ($regexes as $regex) {
                if ($this->matchUserAgent($regex.'[^a-z0-9]+')) {
                    $this->matchedRegex = $regex;
                    return array_search($brand, DeviceParserAbstract::$deviceBrands);
                }
            }
        }

        return '';
    }

    public function getMatchedRegex()
    {
        return $this->matchedRegex;
    }
}
