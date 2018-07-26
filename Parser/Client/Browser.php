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
        '3B' => '360 Browser',
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'AF' => 'ANT Fresco',
        'AG' => 'ANTGalio',
        'AL' => 'Aloha Browser',
        'AM' => 'Amaya',
        'AO' => 'Amigo',
        'AN' => 'Android Browser',
        'AR' => 'Arora',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'AT' => 'Atomic Web Browser',
        'AS' => 'Avast Secure Browser',
        'BB' => 'BlackBerry Browser',
        'BD' => 'Baidu Browser',
        'BS' => 'Baidu Spark',
        'BE' => 'Beonex',
        'BJ' => 'Bunjalloo',
        'BL' => 'B-Line',
        'BR' => 'Brave',
        'BK' => 'BriskBard',
        'BX' => 'BrowseX',
        'CA' => 'Camino',
        'CC' => 'Coc Coc',
        'CD' => 'Comodo Dragon',
        'C1' => 'Coast',
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
        'CY' => 'Cyberfox',
        'CS' => 'Cheshire',
        'CU' => 'Cunaguaro',
        'DB' => 'dbrowser',
        'DE' => 'Deepnet Explorer',
        'DF' => 'Dolphin',
        'DO' => 'Dorado',
        'DL' => 'Dooble',
        'DI' => 'Dillo',
        'EI' => 'Epic',
        'EL' => 'Elinks',
        'EB' => 'Element Browser',
        'EP' => 'GNOME Web',
        'ES' => 'Espial TV Browser',
        'FB' => 'Firebird',
        'FD' => 'Fluid',
        'FE' => 'Fennec',
        'FF' => 'Firefox',
        'FK' => 'Firefox Focus',
        'FL' => 'Flock',
        'FM' => 'Firefox Mobile',
        'FW' => 'Fireweb',
        'FN' => 'Fireweb Navigator',
        'GA' => 'Galeon',
        'GE' => 'Google Earth',
        'HJ' => 'HotJava',
        'IA' => 'Iceape',
        'IB' => 'IBrowse',
        'IC' => 'iCab',
        'I2' => 'iCab Mobile',
        'I1' => 'Iridium',
        'ID' => 'IceDragon',
        'IV' => 'Isivioo',
        'IW' => 'Iceweasel',
        'IE' => 'Internet Explorer',
        'IM' => 'IE Mobile',
        'IR' => 'Iron',
        'JS' => 'Jasmine',
        'JI' => 'Jig Browser',
        'KI' => 'Kindle Browser',
        'KM' => 'K-meleon',
        'KO' => 'Konqueror',
        'KP' => 'Kapiko',
        'KY' => 'Kylo',
        'KZ' => 'Kazehakase',
        'LB' => 'Liebao',
        'LG' => 'LG Browser',
        'LI' => 'Links',
        'LU' => 'LuaKit',
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
        'NE' => 'NetSurf',
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
        'OT' => 'Otter Browser',
        'PL' => 'Palm Blazer',
        'PM' => 'Pale Moon',
        'PP' => 'Oppo Browser',
        'PR' => 'Palm Pre',
        'PU' => 'Puffin',
        'PW' => 'Palm WebPro',
        'PA' => 'Palmscape',
        'PX' => 'Phoenix',
        'PO' => 'Polaris',
        'PT' => 'Polarity',
        'PS' => 'Microsoft Edge',
        'QQ' => 'QQ Browser',
        'QT' => 'Qutebrowser',
        'QZ' => 'QupZilla',
        'RK' => 'Rekonq',
        'RM' => 'RockMelt',
        'SB' => 'Samsung Browser',
        'SA' => 'Sailfish Browser',
        'SC' => 'SEMC-Browser',
        'SE' => 'Sogou Explorer',
        'SF' => 'Safari',
        'SH' => 'Shiira',
        'SK' => 'Skyfire',
        'SS' => 'Seraphic Sraf',
        'SL' => 'Sleipnir',
        'SM' => 'SeaMonkey',
        'SN' => 'Snowshoe',
        'SR' => 'Sunrise',
        'SP' => 'SuperBird',
        'ST' => 'Streamy',
        'SX' => 'Swiftfox',
        'TZ' => 'Tizen Browser',
        'TS' => 'TweakStyle',
        'UC' => 'UC Browser',
        'VI' => 'Vivaldi',
        'VB' => 'Vision Mobile Browser',
        'WE' => 'WebPositive',
        'WF' => 'Waterfox',
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
        'Chrome'             => array('CH', 'BR', 'CC', 'CD', 'CM', 'CI', 'CF', 'CN', 'CR', 'CP', 'IR', 'RM', 'AO', 'TS', 'VI', 'PT', 'AS'),
        'Firefox'            => array('FF', 'FE', 'FM', 'SX', 'FB', 'PX', 'MB', 'EI', 'WF', 'CU'),
        'Internet Explorer'  => array('IE', 'IM', 'PS'),
        'Konqueror'          => array('KO'),
        'NetFront'           => array('NF'),
        'NetSurf'            => array('NE'),
        'Nokia Browser'      => array('NB', 'NO', 'NV', 'DO'),
        'Opera'              => array('OP', 'OM', 'OI', 'ON'),
        'Safari'             => array('SF', 'MF'),
        'Sailfish Browser'   => array('SA'),
    );

    /**
     * Browsers that are available for mobile devices only
     *
     * @var array
     */
    protected static $mobileOnlyBrowsers = array(
        '36', 'PU', 'SK', 'MF', 'OI', 'OM', 'DB', 'ST', 'BL', 'IV', 'FM', 'C1', 'AL', 'SA'
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
     * @param string $browserLabel
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

    /**
     * Returns if the given browser is mobile only
     *
     * @param string $browser  Label or name of browser
     * @return bool
     */
    public static function isMobileOnlyBrowser($browser)
    {
        return in_array($browser, self::$mobileOnlyBrowsers) || (in_array($browser, self::$availableBrowsers) && in_array(array_search($browser, self::$availableBrowsers), self::$mobileOnlyBrowsers));
    }

    public function parse()
    {
        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);
            if ($matches) {
                break;
            }
        }

        if (empty($matches)) {
            return null;
        }

        $name  = $this->buildByMatch($regex['name'], $matches);

        foreach (self::getAvailableBrowsers() as $browserShort => $browserName) {
            if (strtolower($name) == strtolower($browserName)) {
                $version = (string) $this->buildVersion($regex['version'], $matches);
                $engine = $this->buildEngine(isset($regex['engine']) ? $regex['engine'] : array(), $version);
                $engineVersion = $this->buildEngineVersion($engine);
                return array(
                    'type'           => 'browser',
                    'name'           => $browserName,
                    'short_name'     => $browserShort,
                    'version'        => $version,
                    'engine'         => $engine,
                    'engine_version' => $engineVersion,
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
            $engineParser->setYamlParser($this->getYamlParser());
            $engineParser->setCache($this->getCache());
            $engineParser->setUserAgent($this->userAgent);
            $engine = $engineParser->parse();
        }

        return $engine;
    }

    protected function buildEngineVersion($engine)
    {
        $engineVersionParser = new Engine\Version($this->userAgent, $engine);

        return $engineVersionParser->parse();
    }
}
