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
        '3B' => '360 Browser',
        '36' => '360 Phone Browser',
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'AD' => 'AOL Shield',
        'AF' => 'ANT Fresco',
        'AG' => 'ANTGalio',
        'AL' => 'Aloha Browser',
        'AM' => 'Amaya',
        'AN' => 'Android Browser',
        'AO' => 'Amigo',
        'AR' => 'Arora',
        'AS' => 'Avast Secure Browser',
        'AT' => 'Atomic Web Browser',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'BA' => 'Beaker Browser',
        'BB' => 'BlackBerry Browser',
        'BD' => 'Baidu Browser',
        'BE' => 'Beonex',
        'BJ' => 'Bunjalloo',
        'BK' => 'BriskBard',
        'BL' => 'B-Line',
        'BR' => 'Brave',
        'BS' => 'Baidu Spark',
        'BX' => 'BrowseX',
        'C1' => 'Coast',
        'CA' => 'Camino',
        'CC' => 'Coc Coc',
        'CD' => 'Comodo Dragon',
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
        'CU' => 'Cunaguaro',
        'CX' => 'Charon',
        'CY' => 'Cyberfox',
        'DB' => 'dbrowser',
        'DE' => 'Deepnet Explorer',
        'DF' => 'Dolphin',
        'DI' => 'Dillo',
        'DL' => 'Dooble',
        'DO' => 'Dorado',
        'EB' => 'Element Browser',
        'EI' => 'Epic',
        'EL' => 'Elinks',
        'EP' => 'GNOME Web',
        'ES' => 'Espial TV Browser',
        'FB' => 'Firebird',
        'FD' => 'Fluid',
        'FE' => 'Fennec',
        'FF' => 'Firefox',
        'FK' => 'Firefox Focus',
        'FL' => 'Flock',
        'FM' => 'Firefox Mobile',
        'FN' => 'Fireweb Navigator',
        'FR' => 'Firefox Rocket',
        'FW' => 'Fireweb',
        'GA' => 'Galeon',
        'GE' => 'Google Earth',
        'HC' => 'Headless Chrome',
        'HJ' => 'HotJava',
        'I1' => 'Iridium',
        'I2' => 'iCab Mobile',
        'IA' => 'Iceape',
        'IB' => 'IBrowse',
        'IC' => 'iCab',
        'ID' => 'IceDragon',
        'IE' => 'Internet Explorer',
        'IM' => 'IE Mobile',
        'IR' => 'Iron',
        'IV' => 'Isivioo',
        'IW' => 'Iceweasel',
        'JI' => 'Jig Browser',
        'JS' => 'Jasmine',
        'KI' => 'Kindle Browser',
        'KM' => 'K-meleon',
        'KO' => 'Konqueror',
        'KP' => 'Kapiko',
        'KY' => 'Kylo',
        'KZ' => 'Kazehakase',
        'LB' => 'Liebao',
        'LG' => 'LG Browser',
        'LI' => 'Links',
        'LS' => 'Lunascape',
        'LU' => 'LuaKit',
        'LX' => 'Lynx',
        'MB' => 'MicroB',
        'MC' => 'NCSA Mosaic',
        'ME' => 'Mercury',
        'MF' => 'Mobile Safari',
        'MI' => 'Midori',
        'MS' => 'Mobile Silk',
        'MU' => 'MIUI Browser',
        'MX' => 'Maxthon',
        'NB' => 'Nokia Browser',
        'NE' => 'NetSurf',
        'NF' => 'NetFront',
        'NL' => 'NetFront Life',
        'NO' => 'Nokia OSS Browser',
        'NP' => 'NetPositive',
        'NS' => 'Netscape',
        'NT' => 'NTENT Browser',
        'NV' => 'Nokia Ovi Browser',
        'OB' => 'Obigo',
        'OD' => 'Odyssey Web Browser',
        'OE' => 'ONE Browser',
        'OF' => 'Off By One',
        'OI' => 'Opera Mini',
        'OM' => 'Opera Mobile',
        'ON' => 'Opera Next',
        'OO' => 'Opera Touch',
        'OP' => 'Opera',
        'OR' => 'Oregano',
        'OT' => 'Otter Browser',
        'OV' => 'Openwave Mobile Browser',
        'OW' => 'OmniWeb',
        'PA' => 'Palmscape',
        'PL' => 'Palm Blazer',
        'PM' => 'Pale Moon',
        'PO' => 'Polaris',
        'PP' => 'Oppo Browser',
        'PR' => 'Palm Pre',
        'PS' => 'Microsoft Edge',
        'PT' => 'Polarity',
        'PU' => 'Puffin',
        'PW' => 'Palm WebPro',
        'PX' => 'Phoenix',
        'QM' => 'Qwant Mobile',
        'QQ' => 'QQ Browser',
        'QT' => 'Qutebrowser',
        'QZ' => 'QupZilla',
        'RK' => 'Rekonq',
        'RM' => 'RockMelt',
        'SA' => 'Sailfish Browser',
        'SB' => 'Samsung Browser',
        'SC' => 'SEMC-Browser',
        'SE' => 'Sogou Explorer',
        'SF' => 'Safari',
        'SH' => 'Shiira',
        'SK' => 'Skyfire',
        'SL' => 'Sleipnir',
        'SM' => 'SeaMonkey',
        'SN' => 'Snowshoe',
        'SP' => 'SuperBird',
        'SR' => 'Sunrise',
        'SS' => 'Seraphic Sraf',
        'ST' => 'Streamy',
        'SX' => 'Swiftfox',
        'TB' => 'Tenta Browser',
        'TF' => 'TenFourFox',
        'TS' => 'TweakStyle',
        'TZ' => 'Tizen Browser',
        'UC' => 'UC Browser',
        'VB' => 'Vision Mobile Browser',
        'VI' => 'Vivaldi',
        'WE' => 'WebPositive',
        'WF' => 'Waterfox',
        'WO' => 'wOSBrowser',
        'WP' => 'Web Explorer',
        'WT' => 'WeTab Browser',
        'XI' => 'Xiino',
        'YA' => 'Yandex Browser',
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
        'Chrome'             => array('CH', 'BA', 'BR', 'CC', 'CD', 'CM', 'CI', 'CF', 'CN', 'CR', 'CP', 'IR', 'RM', 'AO', 'TS', 'VI', 'PT', 'AS', 'TB', 'AD', 'SB', 'WP'),
        'Firefox'            => array('FF', 'FE', 'FM', 'SX', 'FB', 'PX', 'MB', 'EI', 'WF', 'CU', 'TF', 'QM', 'FR'),
        'Internet Explorer'  => array('IE', 'IM', 'PS'),
        'Konqueror'          => array('KO'),
        'NetFront'           => array('NF'),
        'NetSurf'            => array('NE'),
        'Nokia Browser'      => array('NB', 'NO', 'NV', 'DO'),
        'Opera'              => array('OP', 'OM', 'OI', 'ON', 'OO'),
        'Safari'             => array('SF', 'MF'),
        'Sailfish Browser'   => array('SA')
    );

    /**
     * Browsers that are available for mobile devices only
     *
     * @var array
     */
    protected static $mobileOnlyBrowsers = array(
        '36', 'PU', 'SK', 'MF', 'OI', 'OM', 'DB', 'ST', 'BL', 'IV', 'FM', 'C1', 'AL', 'SA', 'SB', 'FR', 'WP'
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
        throw new \Exception('Detected browser name was not found in $availableBrowsers. Tried to parse user agent: '.$this->userAgent); // @codeCoverageIgnore
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
