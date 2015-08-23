<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

use DeviceDetector\Parser\Client\Browser\Engine;

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
        '36' => '360 Phone Browser',
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'AG' => 'ANTGalio',
        'AM' => 'Amaya',
        'AO' => 'Amigo',
        'AN' => 'Android Browser',
        'AR' => 'Arora',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'BB' => 'BlackBerry Browser',
        'BD' => 'Baidu Browser',
        'BS' => 'Baidu Spark',
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
        'DE' => 'Deepnet Explorer',
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
        'LB' => 'Liebao',
        'LI' => 'Links',
        'LS' => 'Lunascape',
        'LX' => 'Lynx',
        'MB' => 'MicroB',
        'MC' => 'NCSA Mosaic',
        'ME' => 'Mercury',
        'MF' => 'Mobile Safari',
        'MI' => 'Midori',
        'MU' => 'MIUI Browser',
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
        'OD' => 'Odyssey Web Browser',
        'OF' => 'Off By One',
        'OE' => 'ONE Browser',
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
        'PS' => 'Microsoft Edge',
        'QQ' => 'QQ Browser',
        'RK' => 'Rekonq',
        'RM' => 'RockMelt',
        'SA' => 'Sailfish Browser',
        'SC' => 'SEMC-Browser',
        'SE' => 'Sogou Explorer',
        'SF' => 'Safari',
        'SH' => 'Shiira',
        'SL' => 'Sleipnir',
        'SM' => 'SeaMonkey',
        'SN' => 'Snowshoe',
        'SR' => 'Sunrise',
        'SX' => 'Swiftfox',
        'TZ' => 'Tizen Browser',
        'UC' => 'UC Browser',
        'VI' => 'Vivaldi',
        'WE' => 'WebPositive',
        'WO' => 'wOSBrowser',
        'WT' => 'WeTab Browser',
        'YA' => 'Yandex Browser',
        'XI' => 'Xiino'
    );

    /**
     * Browser families mapped to the short codes of the associated browsers
     *
     * @var array
     */
    protected static $browserFamilies = array(
        'Android Browser'    => array('AN', 'MU'),
        'BlackBerry Browser' => array('BB'),
        'Baidu'              => array('BD', 'BS'),
        'Amiga'              => array('AV', 'AW'),
        'Chrome'             => array('CH', 'CD', 'CM', 'CI', 'CF', 'CN', 'CR', 'CP', 'IR', 'RM', 'AO', 'VI'),
        'Firefox'            => array('FF', 'FE', 'SX', 'FB', 'PX', 'MB'),
        'Internet Explorer'  => array('IE', 'IM', 'PS'),
        'Konqueror'          => array('KO'),
        'NetFront'           => array('NF'),
        'Nokia Browser'      => array('NB', 'NO', 'NV'),
        'Opera'              => array('OP', 'OM', 'OI', 'ON'),
        'Safari'             => array('SF', 'MF'),
        'Sailfish Browser'   => array('SA')
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
            if ($matches) {
                break;
            }
        }

        if (!$matches) {
            return null;
        }

        $name  = $this->buildByMatch($regex['name'], $matches);

        foreach (self::getAvailableBrowsers() as $browserShort => $browserName) {
            if (strtolower($name) == strtolower($browserName)) {
                $version = (string) $this->buildVersion($regex['version'], $matches);
                $engine = $this->buildEngine(isset($regex['engine']) ? $regex['engine'] : array(), $version);
                return array(
                    'type'       => 'browser',
                    'name'       => $browserName,
                    'short_name' => $browserShort,
                    'version'    => $version,
                    'engine'     => $engine
                );
            }
        }

        // This Exception should never be thrown. If so a defined browser name is missing in $availableBrowsers
        throw new \Exception('Detected browser name was not found in $availableBrowsers'); // @codeCoverageIgnore
    }

    protected function buildEngine($engineData, $browserVersion)
    {
        $engine = '';
        // if an engine is set as default
        if (isset($engineData['default'])) {
            $engine = $engineData['default'];
        }
        // check if engine is set for browser version
        if (array_key_exists('versions', $engineData) && is_array($engineData['versions'])) {
            foreach ($engineData['versions'] as $version => $versionEngine) {
                if (version_compare($browserVersion, $version) >= 0) {
                    $engine = $versionEngine;
                }
            }
        }
        // try to detect the engine using the regexes
        if (empty($engine)) {
            $engineParser = new Engine();
            $engineParser->setUserAgent($this->userAgent);
            $engine = $engineParser->parse();
        }

        return $engine;
    }
}
