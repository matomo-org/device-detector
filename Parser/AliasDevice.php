<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser;

/**
 * Class AliasDevice
 * @package DeviceDetector\Parser
 * @uses ```php
 *      use DeviceDetector\Parser\AliasDevice;
 *
 *      $userAgent = '';
 *      $parser = new AliasDevice($userAgent);
 *
 * ```
 */
class AliasDevice extends ParserAbstract
{
    protected $fixtureFile = 'regexes/alias_devices.yml';
    protected $parserName = 'alias_device';

    public function parse()
    {
        $return = array();
        $matches = false;

        foreach ($this->getRegexes() as $aliasDeviceRegex) {
            $matches = $this->matchUserAgent($aliasDeviceRegex['regex']);
            if ($matches) {
                break;
            }
        }

        if (!$matches) {
            return $return;
        }

        $name = $this->buildByMatch($aliasDeviceRegex['name'], $matches);
        $name = trim($name);

        return compact('name');
    }

}