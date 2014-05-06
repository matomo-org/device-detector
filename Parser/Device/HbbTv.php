<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Parser\Device;

class HbbTv extends DeviceParserAbstract {

    protected $fixtureFile = 'regexes/device/televisions.yml';

    /**
     * Parses the current UA and checks whether it contains HbbTv information
     *
     * @see televisions.yml for list of detected personal information managers
     */
    public function parse()
    {
        if (!$this->isHbbTv()) {
            return false;
        }

        $result = parent::parse();

        // always set device type to tv, even if no model/brand could be found
        if (!$result) {
            $this->deviceType = 'tv';
        }

        return true;
    }

    /**
     * Returns if the parsed UA was identified as a HbbTV device
     *
     * @return bool
     */
    public function isHbbTv()
    {
        $regex = 'HbbTV/([1-9]{1}(\.[0-9]{1}){1,2})';
        return $this->matchUserAgent($regex);
    }
}