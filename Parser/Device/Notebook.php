<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Device;

/**
 * Class Notebook
 *
 * Device parser for notebook detection in Facebook useragents
 *
 * @package DeviceDetector\Parser\Device
 */
class Notebook extends DeviceParserAbstract
{
    protected $fixtureFile = 'regexes/device/notebooks.yml';
    protected $parserName  = 'notebook';

    public function parse()
    {
        if (!$this->matchUserAgent('FBMD/')) {
            return false;
        }

        return parent::parse();
    }
}
