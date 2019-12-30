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
        '2B' => '2345 Browser',
        '36' => '360 Phone Browser',
        '3B' => '360 Browser',
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'AF' => 'ANT Fresco',
        'AG' => 'ANTGalio',
        'AL' => 'Aloha Browser',
        'AH' => 'Aloha Browser Lite',
        'AM' => 'Amaya',
        'AO' => 'Amigo',
        'AN' => 'Android Browser',
        'AD' => 'AOL Shield',
        'AR' => 'Arora',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'AT' => 'Atomic Web Browser',
        'AS' => 'Avast Secure Browser',
        'VG' => 'AVG Secure Browser',
        'BA' => 'Beaker Browser',
        'BM' => 'Beamrise',
        'BB' => 'BlackBerry Browser',
        'BD' => 'Baidu Browser',
        'BS' => 'Baidu Spark',
        'BI' => 'Basilisk',
        'BE' => 'Beonex',
        'BH' => 'BlackHawk',
        'BJ' => 'Bunjalloo',
        'BL' => 'B-Line',
        'BR' => 'Brave',
        'BK' => 'BriskBard',
        'BX' => 'BrowseX',
        'CA' => 'Camino',
        'CL' => 'CCleaner',
        'CC' => 'Coc Coc',
        'CD' => 'Comodo Dragon',
        'C1' => 'Coast',
        'CX' => 'Charon',
        'CE' => 'CM Browser',
        'CF' => 'Chrome Frame',
        'HC' => 'Headless Chrome',
        'CH' => 'Chrome',
        'CI' => 'Chrome Mobile iOS',
        'CK' => 'Conkeror',
        'CM' => 'Chrome Mobile',
        'CN' => 'CoolNovo',
        'CO' => 'CometBird',
        'CB' => 'COS Browser',
        'CP' => 'ChromePlus',
        'CR' => 'Chromium',
        'CY' => 'Cyberfox',
        'CS' => 'Cheshire',
        'CT' => 'Crusta',
        'CU' => 'Cunaguaro',
        'CV' => 'Chrome Webview',
        'DB' => 'dbrowser',
        'DE' => 'Deepnet Explorer',
        'DT' => 'Delta Browser',
        'DF' => 'Dolphin',
        'DO' => 'Dorado',
        'DL' => 'Dooble',
        'DI' => 'Dillo',
        'DD' => 'DuckDuckGo Privacy Browser',
        'EC' => 'Ecosia',
        'EI' => 'Epic',
        'EL' => 'Elinks',
        'EB' => 'Element Browser',
        'EZ' => 'eZ Browser',
        'EU' => 'EUI Browser',
        'EP' => 'GNOME Web',
        'ES' => 'Espial TV Browser',
        'FA' => 'Falkon',
        'FX' => 'Faux Browser',
        'F1' => 'Firefox Mobile iOS',
        'FB' => 'Firebird',
        'FD' => 'Fluid',
        'FE' => 'Fennec',
        'FF' => 'Firefox',
        'FK' => 'Firefox Focus',
        'FY' => 'Firefox Reality',
        'FR' => 'Firefox Rocket',
        'FL' => 'Flock',
        'FM' => 'Firefox Mobile',
        'FW' => 'Fireweb',
        'FN' => 'Fireweb Navigator',
        'FU' => 'FreeU',
        'GA' => 'Galeon',
        'GE' => 'Google Earth',
        'HA' => 'Hawk Turbo Browser',
        'HO' => 'hola! Browser',
        'HJ' => 'HotJava',
        'HU' => 'Huawei Browser',
        'IB' => 'IBrowse',
        'IC' => 'iCab',
        'I2' => 'iCab Mobile',
        'I1' => 'Iridium',
        'I3' => 'Iron Mobile',
        'I4' => 'IceCat',
        'ID' => 'IceDragon',
        'IV' => 'Isivioo',
        'IW' => 'Iceweasel',
        'IE' => 'Internet Explorer',
        'IM' => 'IE Mobile',
        'IR' => 'Iron',
        'JS' => 'Jasmine',
        'JI' => 'Jig Browser',
        'JO' => 'Jio Browser',
        'KB' => 'K.Browser',
        'KI' => 'Kindle Browser',
        'KM' => 'K-meleon',
        'KO' => 'Konqueror',
        'KP' => 'Kapiko',
        'KN' => 'Kinza',
        'KW' => 'Kiwi',
        'KY' => 'Kylo',
        'KZ' => 'Kazehakase',
        'LB' => 'Cheetah Browser',
        'LF' => 'LieBaoFast',
        'LG' => 'LG Browser',
        'LI' => 'Links',
        'LO' => 'Lovense Browser',
        'LU' => 'LuaKit',
        'LS' => 'Lunascape',
        'LX' => 'Lynx',
        'M1' => 'mCent',
        'MB' => 'MicroB',
        'MC' => 'NCSA Mosaic',
        'MZ' => 'Meizu Browser',
        'ME' => 'Mercury',
        'MF' => 'Mobile Safari',
        'MI' => 'Midori',
        'MO' => 'Mobicip',
        'MU' => 'MIUI Browser',
        'MS' => 'Mobile Silk',
        'MN' => 'Minimo',
        'MT' => 'Mint Browser',
        'MX' => 'Maxthon',
        'NB' => 'Nokia Browser',
        'NO' => 'Nokia OSS Browser',
        'NV' => 'Nokia Ovi Browser',
        'NX' => 'Nox Browser',
        'NE' => 'NetSurf',
        'NF' => 'NetFront',
        'NL' => 'NetFront Life',
        'NP' => 'NetPositive',
        'NS' => 'Netscape',
        'NT' => 'NTENT Browser',
        'OC' => 'Oculus Browser',
        'O1' => 'Opera Mini iOS',
        'OB' => 'Obigo',
        'OD' => 'Odyssey Web Browser',
        'OF' => 'Off By One',
        'OE' => 'ONE Browser',
        'OX' => 'Opera GX',
        'OG' => 'Opera Neon',
        'OH' => 'Opera Devices',
        'OI' => 'Opera Mini',
        'OM' => 'Opera Mobile',
        'OP' => 'Opera',
        'ON' => 'Opera Next',
        'OO' => 'Opera Touch',
        'OS' => 'Ordissimo',
        'OR' => 'Oregano',
        'OY' => 'Origyn Web Browser',
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
        'Q1' => 'QQ Browser Mini',
        'QQ' => 'QQ Browser',
        'QT' => 'Qutebrowser',
        'QZ' => 'QupZilla',
        'QM' => 'Qwant Mobile',
        'QW' => 'QtWebEngine',
        'RE' => 'Realme Browser',
        'RK' => 'Rekonq',
        'RM' => 'RockMelt',
        'SB' => 'Samsung Browser',
        'SA' => 'Sailfish Browser',
        'SC' => 'SEMC-Browser',
        'SE' => 'Sogou Explorer',
        'SF' => 'Safari',
        'SW' => 'SalamWeb',
        'SH' => 'Shiira',
        'S1' => 'SimpleBrowser',
        'SK' => 'Skyfire',
        'SS' => 'Seraphic Sraf',
        'SL' => 'Sleipnir',
        'SN' => 'Snowshoe',
        'SO' => 'Sogou Mobile Browser',
        'S2' => 'Splash',
        'SI' => 'Sputnik Browser',
        'SR' => 'Sunrise',
        'SP' => 'SuperBird',
        'SU' => 'Super Fast Browser',
        'S0' => 'START Internet Browser',
        'ST' => 'Streamy',
        'SX' => 'Swiftfox',
        'SZ' => 'Seznam Browser',
        'TO' => 't-online.de Browser',
        'TA' => 'Tao Browser',
        'TF' => 'TenFourFox',
        'TB' => 'Tenta Browser',
        'TZ' => 'Tizen Browser',
        'TS' => 'TweakStyle',
        'UB' => 'UBrowser',
        'UC' => 'UC Browser',
        'UM' => 'UC Browser Mini',
        'UT' => 'UC Browser Turbo',
        'UZ' => 'Uzbl',
        'VI' => 'Vivaldi',
        'VV' => 'vivo Browser',
        'VB' => 'Vision Mobile Browser',
        'WI' => 'Wear Internet Browser',
        'WP' => 'Web Explorer',
        'WE' => 'WebPositive',
        'WF' => 'Waterfox',
        'WH' => 'Whale Browser',
        'WO' => 'wOSBrowser',
        'WT' => 'WeTab Browser',
        'YA' => 'Yandex Browser',
        'YL' => 'Yandex Browser Lite',
        'XI' => 'Xiino'

        // detected browsers in older versions
        // 'IA' => 'Iceape',  => pim
        // 'SM' => 'SeaMonkey',  => pim
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
        'Chrome'             => array('CH', 'BA', 'BR', 'CC', 'CD', 'CM', 'CI', 'CF', 'CN', 'CR', 'CP', 'DD', 'IR', 'RM', 'AO', 'TS', 'VI', 'PT', 'AS', 'TB', 'AD', 'SB', 'WP', 'I3', 'CV', 'WH', 'SZ', 'QW', 'LF', 'KW', '2B', 'CE', 'EC', 'MT', 'MS', 'HA', 'OC', 'MZ', 'BM', 'KN', 'SW', 'M1', 'FA', 'TA', 'AH', 'CL', 'SU', 'EU', 'UB', 'LO', 'VG'),
        'Firefox'            => array('FF', 'FE', 'FM', 'SX', 'FB', 'PX', 'MB', 'EI', 'WF', 'CU', 'TF', 'QM', 'FR', 'I4', 'GZ', 'MO', 'F1', 'BI', 'MN', 'BH', 'TO', 'OS', 'FY'),
        'Internet Explorer'  => array('IE', 'IM', 'PS'),
        'Konqueror'          => array('KO'),
        'NetFront'           => array('NF'),
        'NetSurf'            => array('NE'),
        'Nokia Browser'      => array('NB', 'NO', 'NV', 'DO'),
        'Opera'              => array('OP', 'OM', 'OI', 'ON', 'OO', 'OG', 'OH', 'O1', 'OX'),
        'Safari'             => array('SF', 'MF', 'SO'),
        'Sailfish Browser'   => array('SA')
    );

    /**
     * Browsers that are available for mobile devices only
     *
     * @var array
     */
    protected static $mobileOnlyBrowsers = array(
        '36', 'OC', 'PU', 'SK', 'MF', 'OI', 'OM', 'DD', 'DB', 'ST', 'BL', 'IV', 'FM', 'C1', 'AL', 'SA', 'SB', 'FR', 'WP', 'HA', 'NX', 'HU', 'VV', 'RE', 'CB', 'MZ', 'UM', 'FK', 'FX', 'WI', 'MN', 'M1', 'AH', 'SU', 'EU', 'EZ', 'UT', 'DT', 'S0'
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
        throw new \Exception(sprintf('Detected browser name "%s" was not found in $availableBrowsers. Tried to parse user agent: %s', $name, $this->userAgent)); // @codeCoverageIgnore
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
