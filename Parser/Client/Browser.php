<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
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
        '1B' => '115 Browser',
        '2B' => '2345 Browser',
        '36' => '360 Phone Browser',
        '3B' => '360 Browser',
        '7B' => '7654 Browser',
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'AF' => 'ANT Fresco',
        'AG' => 'ANTGalio',
        'AL' => 'Aloha Browser',
        'AH' => 'Aloha Browser Lite',
        'AM' => 'Amaya',
        'AO' => 'Amigo',
        'AN' => 'Android Browser',
        'AE' => 'AOL Desktop',
        'AD' => 'AOL Shield',
        'AR' => 'Arora',
        'AX' => 'Arctic Fox',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'AI' => 'Arvin',
        'A0' => 'Atom',
        'AT' => 'Atomic Web Browser',
        'AS' => 'Avast Secure Browser',
        'VG' => 'AVG Secure Browser',
        'AC' => 'Avira Scout',
        'A1' => 'AwoX',
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
        'BU' => 'Blue Browser',
        'BN' => 'Borealis Navigator',
        'BR' => 'Brave',
        'BK' => 'BriskBard',
        'BX' => 'BrowseX',
        'BZ' => 'Browzar',
        'BY' => 'Biyubi',
        'BF' => 'Byffox',
        'CA' => 'Camino',
        'CL' => 'CCleaner',
        'C6' => 'Chedot',
        'C0' => 'Centaury',
        'CC' => 'Coc Coc',
        'C4' => 'CoolBrowser',
        'C2' => 'Colibri',
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
        'CW' => 'Cornowser',
        'C3' => 'Chim Lac',
        'CP' => 'ChromePlus',
        'CR' => 'Chromium',
        'C5' => 'Chromium GOST',
        'CY' => 'Cyberfox',
        'CS' => 'Cheshire',
        'CT' => 'Crusta',
        'CG' => 'Craving Explorer',
        'CZ' => 'Crazy Browser',
        'CU' => 'Cunaguaro',
        'CV' => 'Chrome Webview',
        'DB' => 'dbrowser',
        'DE' => 'Deepnet Explorer',
        'DG' => 'deg-degan',
        'DA' => 'Deledao',
        'DT' => 'Delta Browser',
        'DS' => 'DeskBrowse',
        'DF' => 'Dolphin',
        'DO' => 'Dorado',
        'DR' => 'Dot Browser',
        'DL' => 'Dooble',
        'DI' => 'Dillo',
        'DD' => 'DuckDuckGo Privacy Browser',
        'EC' => 'Ecosia',
        'EW' => 'Edge WebView',
        'EI' => 'Epic',
        'EL' => 'Elinks',
        'EB' => 'Element Browser',
        'EE' => 'Elements Browser',
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
        'FO' => 'Flow',
        'FM' => 'Firefox Mobile',
        'FW' => 'Fireweb',
        'FN' => 'Fireweb Navigator',
        'FS' => 'Flast',
        'FU' => 'FreeU',
        'GA' => 'Galeon',
        'GH' => 'Ghostery Privacy Browser',
        'GI' => 'GinxDroid Browser',
        'GB' => 'Glass Browser',
        'GE' => 'Google Earth',
        'GO' => 'GOG Galaxy',
        'HS' => 'HasBrowser',
        'HA' => 'Hawk Turbo Browser',
        'HE' => 'Helio',
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
        'JB' => 'Japan Browser',
        'JS' => 'Jasmine',
        'JA' => 'JavaFX',
        'JI' => 'Jig Browser',
        'JP' => 'Jig Browser Plus',
        'JO' => 'Jio Browser',
        'KB' => 'K.Browser',
        'KI' => 'Kindle Browser',
        'KM' => 'K-meleon',
        'KO' => 'Konqueror',
        'KP' => 'Kapiko',
        'KN' => 'Kinza',
        'KW' => 'Kiwi',
        'KD' => 'Kode Browser',
        'KY' => 'Kylo',
        'KZ' => 'Kazehakase',
        'LB' => 'Cheetah Browser',
        'LF' => 'LieBaoFast',
        'LG' => 'LG Browser',
        'LH' => 'Light',
        'LI' => 'Links',
        'IF' => 'Lolifox',
        'LO' => 'Lovense Browser',
        'LU' => 'LuaKit',
        'LL' => 'Lulumi',
        'LS' => 'Lunascape',
        'LN' => 'Lunascape Lite',
        'LX' => 'Lynx',
        'MD' => 'Mandarin',
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
        'MA' => 'Maelstrom',
        'NM' => 'MxNitro',
        'MY' => 'Mypal',
        'MR' => 'Monument Browser',
        'MW' => 'MAUI WAP Browser',
        'NW' => 'Navigateur Web',
        'NR' => 'NFS Browser',
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
        'O2' => 'Odin',
        'H2' => 'OceanHero',
        'OD' => 'Odyssey Web Browser',
        'OF' => 'Off By One',
        'HH' => 'OhHai Browser',
        'OE' => 'ONE Browser',
        'OX' => 'Opera GX',
        'OG' => 'Opera Neon',
        'OH' => 'Opera Devices',
        'OI' => 'Opera Mini',
        'OM' => 'Opera Mobile',
        'OP' => 'Opera',
        'ON' => 'Opera Next',
        'OO' => 'Opera Touch',
        'OA' => 'Orca',
        'OS' => 'Ordissimo',
        'OR' => 'Oregano',
        'O0' => 'Origin In-Game Overlay',
        'OY' => 'Origyn Web Browser',
        'OV' => 'Openwave Mobile Browser',
        'O3' => 'OpenFin',
        'OW' => 'OmniWeb',
        'OT' => 'Otter Browser',
        'PL' => 'Palm Blazer',
        'PM' => 'Pale Moon',
        'PY' => 'Polypane',
        'PP' => 'Oppo Browser',
        'PR' => 'Palm Pre',
        'PU' => 'Puffin',
        'PW' => 'Palm WebPro',
        'PA' => 'Palmscape',
        'PE' => 'Perfect Browser',
        'PH' => 'Phantom Browser',
        'PX' => 'Phoenix',
        'PB' => 'Phoenix Browser',
        'PF' => 'PlayFree Browser',
        'PO' => 'Polaris',
        'PT' => 'Polarity',
        'LY' => 'PolyBrowser',
        'PI' => 'PrivacyWall',
        'PS' => 'Microsoft Edge',
        'QA' => 'Qazweb',
        'Q2' => 'QQ Browser Lite',
        'Q1' => 'QQ Browser Mini',
        'QQ' => 'QQ Browser',
        'QT' => 'Qutebrowser',
        'QU' => 'Quark',
        'QZ' => 'QupZilla',
        'QM' => 'Qwant Mobile',
        'QW' => 'QtWebEngine',
        'RE' => 'Realme Browser',
        'RK' => 'Rekonq',
        'RM' => 'RockMelt',
        'SB' => 'Samsung Browser',
        'SA' => 'Sailfish Browser',
        'S8' => 'Seewo Browser',
        'SC' => 'SEMC-Browser',
        'SE' => 'Sogou Explorer',
        'SF' => 'Safari',
        'S5' => 'Safe Exam Browser',
        'SW' => 'SalamWeb',
        'S9' => 'Secure Browser',
        'SV' => 'SFive',
        'SH' => 'Shiira',
        'S1' => 'SimpleBrowser',
        'SY' => 'Sizzy',
        'SK' => 'Skyfire',
        'SS' => 'Seraphic Sraf',
        'SL' => 'Sleipnir',
        'S6' => 'Slimjet',
        'S7' => 'SP Browser',
        'T1' => 'Stampy Browser',
        '7S' => '7Star',
        'LE' => 'Smart Lenovo Browser',
        'OZ' => 'Smooz',
        'SN' => 'Snowshoe',
        'SO' => 'Sogou Mobile Browser',
        'S2' => 'Splash',
        'SI' => 'Sputnik Browser',
        'SR' => 'Sunrise',
        'SP' => 'SuperBird',
        'SU' => 'Super Fast Browser',
        'S3' => 'surf',
        'SG' => 'Stargon',
        'S0' => 'START Internet Browser',
        'S4' => 'Steam In-Game Overlay',
        'ST' => 'Streamy',
        'SX' => 'Swiftfox',
        'SZ' => 'Seznam Browser',
        'TR' => 'T-Browser',
        'TO' => 't-online.de Browser',
        'TA' => 'Tao Browser',
        'TF' => 'TenFourFox',
        'TB' => 'Tenta Browser',
        'TZ' => 'Tizen Browser',
        'TU' => 'Tungsten',
        'TG' => 'ToGate',
        'TS' => 'TweakStyle',
        'TV' => 'TV Bro',
        'UB' => 'UBrowser',
        'UC' => 'UC Browser',
        'UH' => 'UC Browser HD',
        'UM' => 'UC Browser Mini',
        'UT' => 'UC Browser Turbo',
        'UR' => 'UR Browser',
        'UZ' => 'Uzbl',
        'VE' => 'Venus Browser',
        'VI' => 'Vivaldi',
        'VV' => 'vivo Browser',
        'VB' => 'Vision Mobile Browser',
        'VM' => 'VMware AirWatch',
        'WI' => 'Wear Internet Browser',
        'WP' => 'Web Explorer',
        'WE' => 'WebPositive',
        'WF' => 'Waterfox',
        'WH' => 'Whale Browser',
        'WO' => 'wOSBrowser',
        'WT' => 'WeTab Browser',
        'YJ' => 'Yahoo! Japan Browser',
        'YA' => 'Yandex Browser',
        'YL' => 'Yandex Browser Lite',
        'YN' => 'Yaani Browser',
        'YB' => 'Yolo Browser',
        'XS' => 'xStand',
        'XI' => 'Xiino',
        'XV' => 'Xvast',
        'ZE' => 'Zetakey',
        'ZV' => 'Zvu',

        // detected browsers in older versions
        // 'IA' => 'Iceape',  => pim
        // 'SM' => 'SeaMonkey',  => pim
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
            '1B', '2B', '7S', 'A0', 'AC', 'AD', 'AE', 'AH', 'AI',
            'AO', 'AS', 'BA', 'BM', 'BR', 'C2', 'C3', 'C5', 'C4',
            'C6', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CL',
            'CM', 'CN', 'CP', 'CR', 'CV', 'CW', 'DA', 'DD', 'DG',
            'DR', 'EC', 'EE', 'EU', 'EW', 'FA', 'FS', 'GB', 'GI',
            'H2', 'HA', 'HE', 'HH', 'HS', 'I3', 'IR', 'JB', 'KN',
            'KW', 'LF', 'LL', 'LO', 'M1', 'MA', 'MD', 'MR', 'MS',
            'MT', 'MZ', 'NM', 'NR', 'O0', 'O2', 'O3', 'OC', 'PB',
            'PT', 'QU', 'QW', 'RM', 'S4', 'S6', 'S8', 'S9', 'SB',
            'SG', 'SS', 'SU', 'SV', 'SW', 'SY', 'SZ', 'T1', 'TA',
            'TB', 'TG', 'TR', 'TS', 'TU', 'TV', 'UB', 'UR', 'VE',
            'VG', 'VI', 'VM', 'WP', 'WH', 'XV', 'YJ', 'YN',
        ],
        'Firefox'            => [
            'AX', 'BI', 'BF', 'BH', 'BN', 'C0', 'CU', 'EI', 'F1',
            'FB', 'FE', 'FF', 'FM', 'FR', 'FY', 'GZ', 'I4', 'IF',
            'IW', 'LH', 'LY', 'MB', 'MN', 'MO', 'MY', 'OA', 'OS',
            'PI', 'PX', 'QA', 'QM', 'S5', 'SX', 'TF', 'TO', 'WF',
            'ZV',
        ],
        'Internet Explorer'  => ['BZ', 'CZ', 'IE', 'IM', 'PS'],
        'Konqueror'          => ['KO'],
        'NetFront'           => ['NF'],
        'NetSurf'            => ['NE'],
        'Nokia Browser'      => ['DO', 'NB', 'NO', 'NV'],
        'Opera'              => ['O1', 'OG', 'OH', 'OI', 'OM', 'ON', 'OO', 'OP', 'OX'],
        'Safari'             => ['MF', 'S7', 'SF', 'SO'],
        'Sailfish Browser'   => ['SA'],
    ];

    /**
     * Browsers that are available for mobile devices only
     *
     * @var array
     */
    protected static $mobileOnlyBrowsers = [
        '36', 'AH', 'AI', 'BL', 'C1', 'C4', 'CB', 'CW', 'DB',
        'DD', 'DT', 'EU', 'EZ', 'FK', 'FM', 'FR', 'FX', 'GH',
        'GI', 'HA', 'HU', 'IV', 'JB', 'KD', 'M1', 'MF', 'MN',
        'MZ', 'NX', 'OC', 'OI', 'OM', 'OZ', 'PU', 'PI', 'PE',
        'QU', 'RE', 'S0', 'S7', 'SA', 'SB', 'SG', 'SK', 'ST',
        'SU', 'T1', 'UH', 'UM', 'UT', 'VE', 'VV', 'WI', 'WP',
        'YN',
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
     * @param string $browserLabel name or short name
     *
     * @return string|null If null, "Unknown"
     */
    public static function getBrowserFamily(string $browserLabel): ?string
    {
        if (\in_array($browserLabel, self::$availableBrowsers)) {
            $browserLabel = \array_search($browserLabel, self::$availableBrowsers);
        }

        foreach (self::$browserFamilies as $browserFamily => $browserLabels) {
            if (\in_array($browserLabel, $browserLabels)) {
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
        return \in_array($browser, self::$mobileOnlyBrowsers) || (\in_array($browser, self::$availableBrowsers)
                && \in_array(\array_search($browser, self::$availableBrowsers), self::$mobileOnlyBrowsers));
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
            if (\strtolower($name) === \strtolower($browserName)) {
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
                    'family'         => self::getBrowserFamily((string) $browserShort),
                ];
            }
        }

        // This Exception should never be thrown. If so a defined browser name is missing in $availableBrowsers
        throw new \Exception(\sprintf(
            'Detected browser name "%s" was not found in $availableBrowsers. Tried to parse user agent: %s',
            $name,
            $this->userAgent
        )); // @codeCoverageIgnore
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
        if (\array_key_exists('versions', $engineData) && \is_array($engineData['versions'])) {
            foreach ($engineData['versions'] as $version => $versionEngine) {
                if (\version_compare($browserVersion, (string) $version) < 0) {
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
            $engine = $result['engine'] ?? '';
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
        $result              = $engineVersionParser->parse();

        return $result['version'] ?? '';
    }
}
