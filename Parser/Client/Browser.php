<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class Browser
 *
 * Client parser for browser detection
 *
 * @package DeviceDetector\Parser\Client
 */
class Browser extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/browsers.yml';
    protected $parserName = 'browser';

    /**
     * Known browsers mapped to their internal short codes
     *
     * @var array
     */
    protected static $availableBrowsers = array(
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'AG' => 'ANTGalio',
        'AM' => 'Amaya',
        'AN' => 'Android Browser',
        'AR' => 'Arora',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'BB' => 'BlackBerry Browser',
        'BD' => 'Bidu Browser',
        'BE' => 'Beonex',
        'BJ' => 'Bunjalloo',
        'BX' => 'BrowseX',
        'CA' => 'Camino',
        'CD' => 'Comodo Dragon',
        'CX' => 'Charon',
        'CF' => 'Chrome Frame',
        'CH' => 'Chrome',
        'CI' => 'Chrome Mobile iOS',
        'CK' => 'Conkeror',
        'CM' => 'Chrome Mobile',
        'CN' => 'CoolNovo',
        'CO' => 'CometBird',
        'CP' => 'ChromePlus',
        'CR' => 'Chromium',
        'CS' => 'Cheshire',
        'DF' => 'Dolphin',
        'DI' => 'Dillo',
        'EL' => 'Elinks',
        'EP' => 'Epiphany',
        'ES' => 'Espial TV Browser',
        'FB' => 'Firebird',
        'FD' => 'Fluid',
        'FE' => 'Fennec',
        'FF' => 'Firefox',
        'FL' => 'Flock',
        'FN' => 'Fireweb Navigator',
        'GA' => 'Galeon',
        'GE' => 'Google Earth',
        'HJ' => 'HotJava',
        'IA' => 'Iceape',
        'IB' => 'IBrowse',
        'IC' => 'iCab',
        'ID' => 'IceDragon',
        'IW' => 'Iceweasel',
        'IE' => 'Internet Explorer',
        'IM' => 'IE Mobile',
        'IR' => 'Iron',
        'JS' => 'Jasmine',
        'KI' => 'Kindle Browser',
        'KM' => 'K-meleon',
        'KO' => 'Konqueror',
        'KP' => 'Kapiko',
        'KZ' => 'Kazehakase',
        'LB' => 'LB Browser',
        'LG' => 'Lightning',
        'LI' => 'Links',
        'LS' => 'Lunascape',
        'LX' => 'Lynx',
        'MB' => 'MicroB',
        'MC' => 'NCSA Mosaic',
        'ME' => 'Mercury',
        'MF' => 'Mobile Safari',
        'MI' => 'Midori',
        'MS' => 'Mobile Silk',
        'MX' => 'Maxthon',
        'NB' => 'Nokia Browser',
        'NO' => 'Nokia OSS Browser',
        'NV' => 'Nokia Ovi Browser',
        'NF' => 'NetFront',
        'NL' => 'NetFront Life',
        'NP' => 'NetPositive',
        'NS' => 'Netscape',
        'OB' => 'Obigo',
        'OI' => 'Opera Mini',
        'OM' => 'Opera Mobile',
        'OP' => 'Opera',
        'ON' => 'Opera Next',
        'OR' => 'Oregano',
        'OV' => 'Openwave Mobile Browser',
        'OW' => 'OmniWeb',
        'PL' => 'Palm Blazer',
        'PM' => 'Pale Moon',
        'PR' => 'Palm Pre',
        'PU' => 'Puffin',
        'PW' => 'Palm WebPro',
        'PX' => 'Phoenix',
        'PO' => 'Polaris',
        'RK' => 'Rekonq',
        'RM' => 'RockMelt',
        'SA' => 'Sailfish Browser',
        'SF' => 'Safari',
        'SL' => 'Sleipnir',
        'SM' => 'SeaMonkey',
        'SN' => 'Snowshoe',
        'SX' => 'Swiftfox',
        'TZ' => 'Tizen Browser',
        'UC' => 'UC Browser',
        'WE' => 'WebPositive',
        'WO' => 'wOSBrowser',
        'YA' => 'Yandex Browser',
        'XI' => 'Xiino'
    );

    /**
     * Browser families mapped to the short codes of the associated browsers
     *
     * @var array
     */
    protected static $browserFamilies = array(
        'Android Browser'    => array('AN'),
        'BlackBerry Browser' => array('BB'),
        'Chrome'             => array('CH', 'CD', 'CM', 'CI', 'CF', 'CN', 'CR', 'CP', 'RM'),
        'Firefox'            => array('FF', 'FE', 'SX', 'FB', 'PX', 'MB'),
        'Internet Explorer'  => array('IE', 'IM'),
        'Konqueror'          => array('KO'),
        'NetFront'           => array('NF'),
        'Nokia Browser'      => array('NB', 'NO', 'NV'),
        'Opera'              => array('OP', 'OM', 'OI', 'ON'),
        'Safari'             => array('SF', 'MF'),
        'Sailfish Browser'   => array('SA'),
        'Bidu Browser'       => array('BD'),
        'LB Browser'         => array('LB')
    );

    /**
     * Returns list of all available browsers
     * @return array
     */
    public static function getAvailableBrowsers()
    {
        return self::$availableBrowsers;
    }

    /**
     * Returns list of all available browser families
     * @return array
     */
    public static function getAvailableBrowserFamilies()
    {
        return self::$browserFamilies;
    }


    /**
     * @param $browserLabel
     * @return bool|string If false, "Unknown"
     */
    public static function getBrowserFamily($browserLabel)
    {
        foreach (self::$browserFamilies as $browserFamily => $browserLabels) {
            if (in_array($browserLabel, $browserLabels)) {
                return $browserFamily;
            }
        }
        return false;
    }

    public function parse()
    {
        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);
            if ($matches)
                break;
        }

        if (!$matches) {
            return null;
        }

        $name  = $this->buildByMatch($regex['name'], $matches);
        $short = 'XX';

        foreach (self::getAvailableBrowsers() AS $browserShort => $browserName) {
            if (strtolower($name) == strtolower($browserName)) {
                $name  = $browserName;
                $short = $browserShort;
            }
        }

        if ($short != 'XX') {
            return array(
                'type'       => 'browser',
                'name'       => $name,
                'short_name' => $short,
                'version'    => $this->buildVersion($regex['version'], $matches)
            );
        }

        return null;
    }
}
