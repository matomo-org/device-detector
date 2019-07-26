<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Client;

use DeviceDetector\Parser\Client\Browser\Engine;

/**
 * Class Browser
 *
 * Client parser for browser detection
 */
class Browser extends AbstractClientParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/client/browsers.yml';

    /**
     * @var string
     */
    protected $parserName = 'browser';

    /**
     * Known browsers mapped to their internal short codes
     *
     * @var array
     */
    protected static $availableBrowsers = [
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
        'AD' => 'AOL Shield',
        'AR' => 'Arora',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'AT' => 'Atomic Web Browser',
        'AS' => 'Avast Secure Browser',
        'BA' => 'Beaker Browser',
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
        'HC' => 'Headless Chrome',
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
        'FR' => 'Firefox Rocket',
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
        'NT' => 'NTENT Browser',
        'OB' => 'Obigo',
        'OD' => 'Odyssey Web Browser',
        'OF' => 'Off By One',
        'OE' => 'ONE Browser',
        'OI' => 'Opera Mini',
        'OM' => 'Opera Mobile',
        'OP' => 'Opera',
        'ON' => 'Opera Next',
        'OO' => 'Opera Touch',
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
        'QM' => 'Qwant Mobile',
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
        'TF' => 'TenFourFox',
        'TB' => 'Tenta Browser',
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
        'XI' => 'Xiino',
    ];

    /**
     * Browser families mapped to the short codes of the associated browsers
     *
     * @var array
     */
    protected static $browserFamilies = [
        'Android Browser'    => ['AN', 'MU'],
        'BlackBerry Browser' => ['BB'],
        'Baidu'              => ['BD', 'BS'],
        'Amiga'              => ['AV', 'AW'],
        'Chrome'             => [
            'CH', 'BA', 'BR', 'CC', 'CD', 'CM', 'CI', 'CF', 'CN',
            'CR', 'CP', 'IR', 'RM', 'AO', 'TS', 'VI', 'PT', 'AS',
            'TB', 'AD', 'SB',
        ],
        'Firefox'            => ['FF', 'FE', 'FM', 'SX', 'FB', 'PX', 'MB', 'EI', 'WF', 'CU', 'TF', 'QM', 'FR'],
        'Internet Explorer'  => ['IE', 'IM', 'PS'],
        'Konqueror'          => ['KO'],
        'NetFront'           => ['NF'],
        'NetSurf'            => ['NE'],
        'Nokia Browser'      => ['NB', 'NO', 'NV', 'DO'],
        'Opera'              => ['OP', 'OM', 'OI', 'ON', 'OO'],
        'Safari'             => ['SF', 'MF'],
        'Sailfish Browser'   => ['SA'],
    ];

    /**
     * Browsers that are available for mobile devices only
     *
     * @var array
     */
    protected static $mobileOnlyBrowsers = [
        '36', 'PU', 'SK', 'MF', 'OI', 'OM', 'DB', 'ST', 'BL', 'IV', 'FM', 'C1', 'AL', 'SA', 'SB', 'FR',
    ];

    /**
     * Returns list of all available browsers
     * @return array
     */
    public static function getAvailableBrowsers(): array
    {
        return self::$availableBrowsers;
    }

    /**
     * Returns list of all available browser families
     * @return array
     */
    public static function getAvailableBrowserFamilies(): array
    {
        return self::$browserFamilies;
    }


    /**
     * @param string $browserLabel
     *
     * @return string|null If null, "Unknown"
     */
    public static function getBrowserFamily(string $browserLabel): ?string
    {
        foreach (self::$browserFamilies as $browserFamily => $browserLabels) {
            if (in_array($browserLabel, $browserLabels)) {
                return $browserFamily;
            }
        }

        return null;
    }

    /**
     * Returns if the given browser is mobile only
     *
     * @param string $browser Label or name of browser
     *
     * @return bool
     */
    public static function isMobileOnlyBrowser(string $browser): bool
    {
        return in_array($browser, self::$mobileOnlyBrowsers) || (in_array($browser, self::$availableBrowsers)
                && in_array(array_search($browser, self::$availableBrowsers), self::$mobileOnlyBrowsers));
    }

    /**
     * @inheritdoc
     */
    public function parse(): ?array
    {
        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);

            if ($matches) {
                break;
            }
        }

        if (empty($matches) || empty($regex)) {
            return null;
        }

        $name = $this->buildByMatch($regex['name'], $matches);

        foreach (self::getAvailableBrowsers() as $browserShort => $browserName) {
            if (strtolower($name) === strtolower($browserName)) {
                $version       = $this->buildVersion((string) $regex['version'], $matches);
                $engine        = $this->buildEngine($regex['engine'] ?? [], $version);
                $engineVersion = $this->buildEngineVersion($engine);

                return [
                    'type'           => 'browser',
                    'name'           => $browserName,
                    'short_name'     => (string) $browserShort,
                    'version'        => $version,
                    'engine'         => $engine,
                    'engine_version' => $engineVersion,
                ];
            }
        }

        // This Exception should never be thrown. If so a defined browser name is missing in $availableBrowsers
        throw new \Exception(sprintf('Detected browser name was not found in $availableBrowsers. Tried to parse user agent: %s', $this->userAgent)); // @codeCoverageIgnore
    }

    /**
     * @param array  $engineData
     * @param string $browserVersion
     *
     * @return string
     */
    protected function buildEngine(array $engineData, string $browserVersion): string
    {
        $engine = '';

        // if an engine is set as default
        if (isset($engineData['default'])) {
            $engine = $engineData['default'];
        }

        // check if engine is set for browser version
        if (array_key_exists('versions', $engineData) && is_array($engineData['versions'])) {
            foreach ($engineData['versions'] as $version => $versionEngine) {
                if (version_compare($browserVersion, (string) $version) < 0) {
                    continue;
                }

                $engine = $versionEngine;
            }
        }

        // try to detect the engine using the regexes
        if (empty($engine)) {
            $engineParser = new Engine();
            $engineParser->setYamlParser($this->getYamlParser());
            $engineParser->setCache($this->getCache());
            $engineParser->setUserAgent($this->userAgent);
            $result = $engineParser->parse();
            $engine = $result['engine'] ?: '';
        }

        return $engine;
    }

    /**
     * @param string $engine
     *
     * @return string
     */
    protected function buildEngineVersion(string $engine): string
    {
        $engineVersionParser = new Engine\Version($this->userAgent, $engine);

        $result = $engineVersionParser->parse();

        return $result['version'] ?: '';
    }
}
