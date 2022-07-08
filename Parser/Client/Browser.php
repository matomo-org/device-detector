<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser\Client;

use DeviceDetector\ClientHints;
use DeviceDetector\Parser\Client\Browser\Engine;
use DeviceDetector\Parser\Client\Hints\BrowserHints;

/**
 * Class Browser
 *
 * Client parser for browser detection
 */
class Browser extends AbstractClientParser
{
    /**
     * @var BrowserHints|null
     */
    private $browserHints;

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
        'V1' => 'Via',
        '1P' => 'Pure Mini Browser',
        '4P' => 'Pure Lite Browser',
        '1R' => 'Raise Fast Browser',
        'R1' => 'Rabbit Private Browser',
        'FQ' => 'Fast Browser UC Lite',
        'FJ' => 'Fast Explorer',
        '1L' => 'Lightning Browser',
        '1C' => 'Cake Browser',
        '1I' => 'IE Browser Fast',
        '1V' => 'Vegas Browser',
        '1O' => 'OH Browser',
        '3O' => 'OH Private Browser',
        '1X' => 'XBrowser Mini',
        '1S' => 'Sharkee Browser',
        '2L' => 'Lark Browser',
        '3P' => 'Pluma',
        '1A' => 'Anka Browser',
        'AZ' => 'Azka Browser',
        '1D' => 'Dragon Browser',
        '1E' => 'Easy Browser',
        'DW' => 'Dark Web Browser',
        '1B' => '115 Browser',
        '2B' => '2345 Browser',
        '36' => '360 Phone Browser',
        '3B' => '360 Browser',
        '7B' => '7654 Browser',
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'BW' => 'AdBlock Browser',
        'AF' => 'ANT Fresco',
        'AG' => 'ANTGalio',
        'AL' => 'Aloha Browser',
        'AH' => 'Aloha Browser Lite',
        'AM' => 'Amaya',
        'A3' => 'Amaze Browser',
        'AO' => 'Amigo',
        'AN' => 'Android Browser',
        'AE' => 'AOL Desktop',
        'AD' => 'AOL Shield',
        'A4' => 'AOL Shield Pro',
        'AP' => 'APUS Browser',
        'AR' => 'Arora',
        'AX' => 'Arctic Fox',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'AI' => 'Arvin',
        'AK' => 'Ask.com',
        'AU' => 'Asus Browser',
        'A0' => 'Atom',
        'AT' => 'Atomic Web Browser',
        'A2' => 'Atlas',
        'AS' => 'Avast Secure Browser',
        'VG' => 'AVG Secure Browser',
        'AC' => 'Avira Scout',
        'A1' => 'AwoX',
        'BA' => 'Beaker Browser',
        'BM' => 'Beamrise',
        'BB' => 'BlackBerry Browser',
        'H1' => 'BrowseHere',
        'BD' => 'Baidu Browser',
        'BS' => 'Baidu Spark',
        'BI' => 'Basilisk',
        'BV' => 'Belva Browser',
        'BE' => 'Beonex',
        'B2' => 'Berry Browser',
        'BT' => 'Bitchute Browser',
        'BH' => 'BlackHawk',
        'B0' => 'Bloket',
        'BJ' => 'Bunjalloo',
        'BL' => 'B-Line',
        'BU' => 'Blue Browser',
        'BO' => 'Bonsai',
        'BN' => 'Borealis Navigator',
        'BR' => 'Brave',
        'BK' => 'BriskBard',
        'B3' => 'Browspeed Browser',
        'BX' => 'BrowseX',
        'BZ' => 'Browzar',
        'BY' => 'Biyubi',
        'BF' => 'Byffox',
        'B4' => 'BF Browser',
        'CA' => 'Camino',
        'CL' => 'CCleaner',
        'C8' => 'CG Browser',
        'CJ' => 'ChanjetCloud',
        'C6' => 'Chedot',
        'C9' => 'Cherry Browser',
        'C0' => 'Centaury',
        'CC' => 'Coc Coc',
        'C4' => 'CoolBrowser',
        'C2' => 'Colibri',
        'CD' => 'Comodo Dragon',
        'C1' => 'Coast',
        'CX' => 'Charon',
        'CE' => 'CM Browser',
        'C7' => 'CM Mini',
        'CF' => 'Chrome Frame',
        'HC' => 'Headless Chrome',
        'CH' => 'Chrome',
        'CI' => 'Chrome Mobile iOS',
        'CK' => 'Conkeror',
        'CM' => 'Chrome Mobile',
        '3C' => 'Chowbo',
        'CN' => 'CoolNovo',
        'CO' => 'CometBird',
        '2C' => 'Comfort Browser',
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
        'YC' => 'CyBrowser',
        'DB' => 'dbrowser',
        'PD' => 'Peeps dBrowser',
        'D1' => 'Debuggable Browser',
        'DC' => 'Decentr',
        'DE' => 'Deepnet Explorer',
        'DG' => 'deg-degan',
        'DA' => 'Deledao',
        'DT' => 'Delta Browser',
        'D0' => 'Desi Browser',
        'DS' => 'DeskBrowse',
        'DF' => 'Dolphin',
        'DZ' => 'Dolphin Zero',
        'DO' => 'Dorado',
        'DR' => 'Dot Browser',
        'DL' => 'Dooble',
        'DI' => 'Dillo',
        'DU' => 'DUC Browser',
        'DD' => 'DuckDuckGo Privacy Browser',
        'EC' => 'Ecosia',
        'EW' => 'Edge WebView',
        'EI' => 'Epic',
        'EL' => 'Elinks',
        'EN' => 'EinkBro',
        'EB' => 'Element Browser',
        'EE' => 'Elements Browser',
        'EZ' => 'eZ Browser',
        'EU' => 'EUI Browser',
        'EP' => 'GNOME Web',
        'G1' => 'G Browser',
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
        '1F' => 'Firefox Klar',
        'F0' => 'Float Browser',
        'FL' => 'Flock',
        'FP' => 'Floorp',
        'FO' => 'Flow',
        'F2' => 'Flow Browser',
        'FM' => 'Firefox Mobile',
        'FW' => 'Fireweb',
        'FN' => 'Fireweb Navigator',
        'FH' => 'Flash Browser',
        'FS' => 'Flast',
        'FU' => 'FreeU',
        'F3' => 'Frost+',
        'FI' => 'Fulldive',
        'GA' => 'Galeon',
        'G8' => 'Gener8',
        'GH' => 'Ghostery Privacy Browser',
        'GI' => 'GinxDroid Browser',
        'GB' => 'Glass Browser',
        'GE' => 'Google Earth',
        'GP' => 'Google Earth Pro',
        'GO' => 'GOG Galaxy',
        'GR' => 'GoBrowser',
        'HB' => 'Harman Browser',
        'HS' => 'HasBrowser',
        'HA' => 'Hawk Turbo Browser',
        'HQ' => 'Hawk Quick Browser',
        'HE' => 'Helio',
        'HX' => 'Hexa Web Browser',
        'HI' => 'Hi Browser',
        'HO' => 'hola! Browser',
        'HJ' => 'HotJava',
        'HU' => 'Huawei Browser Mobile',
        'HP' => 'Huawei Browser',
        'H3' => 'HUB Browser',
        'IO' => 'iBrowser',
        'IS' => 'iBrowser Mini',
        'IB' => 'IBrowse',
        'I6' => 'iDesktop PC Browser',
        'IC' => 'iCab',
        'I2' => 'iCab Mobile',
        'I1' => 'Iridium',
        'I3' => 'Iron Mobile',
        'I4' => 'IceCat',
        'ID' => 'IceDragon',
        'IV' => 'Isivioo',
        'IW' => 'Iceweasel',
        'IN' => 'Inspect Browser',
        'IE' => 'Internet Explorer',
        'I7' => 'Internet Browser Secure',
        'I5' => 'Indian UC Mini Browser',
        'IM' => 'IE Mobile',
        'IR' => 'Iron',
        'JB' => 'Japan Browser',
        'JS' => 'Jasmine',
        'JA' => 'JavaFX',
        'JL' => 'Jelly',
        'JI' => 'Jig Browser',
        'JP' => 'Jig Browser Plus',
        'JO' => 'Jio Browser',
        'J1' => 'JioPages',
        'KB' => 'K.Browser',
        'KF' => 'Keepsafe Browser',
        'KS' => 'Kids Safe Browser',
        'KI' => 'Kindle Browser',
        'KM' => 'K-meleon',
        'KO' => 'Konqueror',
        'KP' => 'Kapiko',
        'KN' => 'Kinza',
        'KW' => 'Kiwi',
        'KD' => 'Kode Browser',
        'KT' => 'KUTO Mini Browser',
        'KY' => 'Kylo',
        'KZ' => 'Kazehakase',
        'LB' => 'Cheetah Browser',
        'LA' => 'Lagatos Browser',
        'LR' => 'Lexi Browser',
        'LV' => 'Lenovo Browser',
        'LF' => 'LieBaoFast',
        'LG' => 'LG Browser',
        'LH' => 'Light',
        'L1' => 'Lilo',
        'LI' => 'Links',
        'IF' => 'Lolifox',
        'LO' => 'Lovense Browser',
        'LT' => 'LT Browser',
        'LU' => 'LuaKit',
        'LL' => 'Lulumi',
        'LS' => 'Lunascape',
        'LN' => 'Lunascape Lite',
        'LX' => 'Lynx',
        'L2' => 'Lynket Browser',
        'MD' => 'Mandarin',
        'M1' => 'mCent',
        'MB' => 'MicroB',
        'MC' => 'NCSA Mosaic',
        'MZ' => 'Meizu Browser',
        'ME' => 'Mercury',
        'M2' => 'Me Browser',
        'MF' => 'Mobile Safari',
        'MI' => 'Midori',
        'M3' => 'Midori Lite',
        'MO' => 'Mobicip',
        'MU' => 'MIUI Browser',
        'MS' => 'Mobile Silk',
        'MN' => 'Minimo',
        'MT' => 'Mint Browser',
        'MX' => 'Maxthon',
        'M4' => 'MaxTube Browser',
        'MA' => 'Maelstrom',
        'MM' => 'Mmx Browser',
        'NM' => 'MxNitro',
        'MY' => 'Mypal',
        'MR' => 'Monument Browser',
        'MW' => 'MAUI WAP Browser',
        'NA' => 'Navegador',
        'NW' => 'Navigateur Web',
        'NK' => 'Naked Browser',
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
        '2O' => 'Odin Browser',
        'H2' => 'OceanHero',
        'OD' => 'Odyssey Web Browser',
        'OF' => 'Off By One',
        'O5' => 'Office Browser',
        'HH' => 'OhHai Browser',
        'OE' => 'ONE Browser',
        'Y1' => 'Opera Crypto',
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
        'O4' => 'Open Browser',
        '4U' => 'Open Browser 4U',
        'OW' => 'OmniWeb',
        'OT' => 'Otter Browser',
        'PL' => 'Palm Blazer',
        'PM' => 'Pale Moon',
        'PY' => 'Polypane',
        'PP' => 'Oppo Browser',
        'PR' => 'Palm Pre',
        'PU' => 'Puffin',
        '2P' => 'Puffin Web Browser',
        'PW' => 'Palm WebPro',
        'PA' => 'Palmscape',
        'PE' => 'Perfect Browser',
        'P1' => 'Phantom.me',
        'PH' => 'Phantom Browser',
        'PX' => 'Phoenix',
        'PB' => 'Phoenix Browser',
        'PF' => 'PlayFree Browser',
        'PK' => 'PocketBook Browser',
        'PO' => 'Polaris',
        'PT' => 'Polarity',
        'LY' => 'PolyBrowser',
        'PI' => 'PrivacyWall',
        'P2' => 'Pi Browser',
        'P0' => 'PronHub Browser',
        'PC' => 'PSI Secure Browser',
        'RW' => 'Reqwireless WebViewer',
        'PS' => 'Microsoft Edge',
        'QA' => 'Qazweb',
        'Q2' => 'QQ Browser Lite',
        'Q1' => 'QQ Browser Mini',
        'QQ' => 'QQ Browser',
        'QS' => 'Quick Browser',
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
        'SO' => 'Sogou Mobile Browser',
        'RF' => 'SOTI Surf',
        '2S' => 'Soul Browser',
        'SF' => 'Safari',
        'PV' => 'Safari Technology Preview',
        'S5' => 'Safe Exam Browser',
        'SW' => 'SalamWeb',
        'VN' => 'Savannah Browser',
        'SD' => 'SavySoda',
        'S9' => 'Secure Browser',
        'SV' => 'SFive',
        'SH' => 'Shiira',
        'K1' => 'Sidekick',
        'S1' => 'SimpleBrowser',
        '3S' => 'SilverMob US',
        'SY' => 'Sizzy',
        'SK' => 'Skyfire',
        'SS' => 'Seraphic Sraf',
        'KK' => 'SiteKiosk',
        'SL' => 'Sleipnir',
        'S6' => 'Slimjet',
        'S7' => 'SP Browser',
        '8S' => 'Secure Private Browser',
        'T1' => 'Stampy Browser',
        '7S' => '7Star',
        'SQ' => 'Smart Browser',
        '6S' => 'Smart Search & Web Browser',
        'LE' => 'Smart Lenovo Browser',
        'OZ' => 'Smooz',
        'SN' => 'Snowshoe',
        'B1' => 'Spectre Browser',
        'S2' => 'Splash',
        'SI' => 'Sputnik Browser',
        'SR' => 'Sunrise',
        'SP' => 'SuperBird',
        'SU' => 'Super Fast Browser',
        '5S' => 'SuperFast Browser',
        'HR' => 'Sushi Browser',
        'S3' => 'surf',
        '4S' => 'Surf Browser',
        'SG' => 'Stargon',
        'S0' => 'START Internet Browser',
        'S4' => 'Steam In-Game Overlay',
        'ST' => 'Streamy',
        'SX' => 'Swiftfox',
        'SZ' => 'Seznam Browser',
        'TP' => 'T+Browser',
        'TR' => 'T-Browser',
        'TO' => 't-online.de Browser',
        'TA' => 'Tao Browser',
        'TF' => 'TenFourFox',
        'TB' => 'Tenta Browser',
        'TE' => 'Tesla Browser',
        'TZ' => 'Tizen Browser',
        'TU' => 'Tungsten',
        'TG' => 'ToGate',
        'TS' => 'TweakStyle',
        'TV' => 'TV Bro',
        'U0' => 'U Browser',
        'UB' => 'UBrowser',
        'UC' => 'UC Browser',
        'UH' => 'UC Browser HD',
        'UM' => 'UC Browser Mini',
        'UT' => 'UC Browser Turbo',
        'UI' => 'Ui Browser Mini',
        'UR' => 'UR Browser',
        'UZ' => 'Uzbl',
        'UE' => 'Ume Browser',
        'V0' => 'vBrowser',
        'VA' => 'Vast Browser',
        'VE' => 'Venus Browser',
        'N0' => 'Nova Video Downloader Pro',
        'VS' => 'Viasat Browser',
        'VI' => 'Vivaldi',
        'VV' => 'vivo Browser',
        'V2' => 'Vivid Browser Mini',
        'VB' => 'Vision Mobile Browser',
        'VM' => 'VMware AirWatch',
        'WI' => 'Wear Internet Browser',
        'WP' => 'Web Explorer',
        'WE' => 'WebPositive',
        'WF' => 'Waterfox',
        'WB' => 'Wave Browser',
        'WH' => 'Whale Browser',
        'WO' => 'wOSBrowser',
        'WT' => 'WeTab Browser',
        'YJ' => 'Yahoo! Japan Browser',
        'YA' => 'Yandex Browser',
        'YL' => 'Yandex Browser Lite',
        'YN' => 'Yaani Browser',
        'Y2' => 'Yo Browser',
        'YB' => 'Yolo Browser',
        'YO' => 'YouCare',
        'YZ' => 'Yuzu Browser',
        'X0' => 'X-VPN',
        'XS' => 'xStand',
        'XI' => 'Xiino',
        'XO' => 'Xooloo Internet',
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
            '1B', '2B', '7S', 'A0', 'AC', 'A4', 'AE', 'AH', 'AI',
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
            'VG', 'VI', 'VM', 'WP', 'WH', 'XV', 'YJ', 'YN', 'FH',
            'B1', 'BO', 'HB', 'PC', 'LA', 'LT', 'PD', 'HR', 'HU',
            'HP', 'IO', 'TP', 'CJ', 'HQ', 'HI', 'NA', 'BW', 'YO',
            'DC', 'G8', 'DT', 'AP', 'AK', 'UI', 'SD', 'VN', '4S',
            '2S', 'RF', 'LR', 'SQ', 'BV', 'L1', 'F0', 'KS', 'V0',
            'C8', 'AZ', 'MM', 'BT', 'N0', 'P0', 'F3', 'VS', 'DU',
            'D0', 'P1', 'O4', '8S', 'H3', 'TE', 'WB', 'K1', 'P2',
            'XO', 'U0', 'B0', 'VA', 'X0', 'NX', 'O5', 'R1',
        ],
        'Firefox'            => [
            'AX', 'BI', 'BF', 'BH', 'BN', 'C0', 'CU', 'EI', 'F1',
            'FB', 'FE', 'FF', 'FM', 'FR', 'FY', 'GZ', 'I4', 'IF',
            'IW', 'LH', 'LY', 'MB', 'MN', 'MO', 'MY', 'OA', 'OS',
            'PI', 'PX', 'QA', 'QM', 'S5', 'SX', 'TF', 'TO', 'WF',
            'ZV', 'FP', 'AD',
        ],
        'Internet Explorer'  => ['BZ', 'CZ', 'IE', 'IM', 'PS'],
        'Konqueror'          => ['KO'],
        'NetFront'           => ['NF'],
        'NetSurf'            => ['NE'],
        'Nokia Browser'      => ['DO', 'NB', 'NO', 'NV'],
        'Opera'              => ['O1', 'OG', 'OH', 'OI', 'OM', 'ON', 'OO', 'OP', 'OX', 'Y1'],
        'Safari'             => ['MF', 'S7', 'SF', 'SO', 'PV'],
        'Sailfish Browser'   => ['SA'],
    ];

    /**
     * Browsers that are available for mobile devices only
     *
     * @var array<string>
     */
    protected static $mobileOnlyBrowsers = [
        '36', 'AH', 'AI', 'BL', 'C1', 'C4', 'CB', 'CW', 'DB',
        'DD', 'DT', 'EU', 'EZ', 'FK', 'FM', 'FR', 'FX', 'GH',
        'GI', 'GR', 'HA', 'HU', 'IV', 'JB', 'KD', 'M1', 'MF',
        'MN', 'MZ', 'NX', 'OC', 'OI', 'OM', 'OZ', 'PU', 'PI',
        'PE', 'QU', 'RE', 'S0', 'S7', 'SA', 'SB', 'SG', 'SK',
        'ST', 'SU', 'T1', 'UH', 'UM', 'UT', 'VE', 'VV', 'WI',
        'WP', 'YN', 'IO', 'IS', 'HQ', 'RW', 'HI', 'NA', 'BW',
        'YO', 'PK', 'MR', 'AP', 'AK', 'UI', 'SD', 'VN', '4S',
        'RF', 'LR', 'SQ', 'BV', 'L1', 'F0', 'KS', 'V0', 'C8',
        'AZ', 'MM', 'BT', 'N0', 'P0', 'F3', 'DU', 'D0', 'P1',
        'O4', 'XO', 'U0', 'B0', 'VA', 'X0',
    ];

    /**
     * Contains a list of mappings from OS names we use to known client hint values
     *
     * @var array<string, array<string>>
     */
    protected static $clientHintMapping = [
        'Chrome' => ['Google Chrome'],
    ];

    /**
     * Browser constructor.
     *
     * @param string           $ua
     * @param ClientHints|null $clientHints
     */
    public function __construct(string $ua = '', ?ClientHints $clientHints = null)
    {
        $this->browserHints = new BrowserHints($ua, $clientHints);
        parent::__construct($ua, $clientHints);
    }

    /**
     * Sets the client hints to parse
     *
     * @param ?ClientHints $clientHints client hints
     */
    public function setClientHints(?ClientHints $clientHints): void
    {
        parent::setClientHints($clientHints);
        $this->browserHints->setClientHints($clientHints);
    }

    /**
     * Sets the user agent to parse
     *
     * @param string $ua user agent
     */
    public function setUserAgent(string $ua): void
    {
        parent::setUserAgent($ua);
        $this->browserHints->setUserAgent($ua);
    }

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
     * @param string $name name browser
     *
     * @return string
     */
    public static function getBrowserShortName(string $name): ?string
    {
        foreach (self::getAvailableBrowsers() as $browserShort => $browserName) {
            if (\strtolower($name) === \strtolower($browserName)) {
                return (string) $browserShort;
            }
        }

        return null;
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
        $browserFromClientHints = $this->parseBrowserFromClientHints();
        $browserFromUserAgent   = $this->parseBrowserFromUserAgent();

        // use client hints in favor of user agent data if possible
        if (!empty($browserFromClientHints['name']) && !empty($browserFromClientHints['version'])) {
            $name          = $browserFromClientHints['name'];
            $version       = $browserFromClientHints['version'];
            $short         = $browserFromClientHints['short_name'];
            $engine        = '';
            $engineVersion = '';

            // If client hints report Chromium, but user agent detects a chromium based browser, we favor this instead
            if ('Chromium' === $name
                && !empty($browserFromUserAgent['name'])
                && 'Chromium' !== $browserFromUserAgent['name']
                && 'Chrome' === self::getBrowserFamily($browserFromUserAgent['name'])
            ) {
                $name    = $browserFromUserAgent['name'];
                $short   = $browserFromUserAgent['short_name'];
                $version = $browserFromUserAgent['version'];
            }

            // Fix mobile browser names e.g. Chrome => Chrome Mobile
            if ($name . ' Mobile' === $browserFromUserAgent['name']) {
                $name  = $browserFromUserAgent['name'];
                $short = $browserFromUserAgent['short_name'];
            }

            // If useragent detects another browser, but the family matches, we use the detected engine from useragent
            if ($name !== $browserFromUserAgent['name']
                && self::getBrowserFamily($name) === self::getBrowserFamily($browserFromUserAgent['name'])
            ) {
                $engine        = $browserFromUserAgent['engine'] ?? '';
                $engineVersion = $browserFromUserAgent['engine_version'] ?? '';
            }

            if ($name === $browserFromUserAgent['name']) {
                $engine        = $browserFromUserAgent['engine'] ?? '';
                $engineVersion = $browserFromUserAgent['engine_version'] ?? '';

                // In case the user agent reports a more detailed version, we try to use this instead
                if (!empty($browserFromUserAgent['version'])
                    && 0 === \strpos($browserFromUserAgent['version'], $version)
                    && \version_compare($version, $browserFromUserAgent['version'], '<')
                ) {
                    $version = $browserFromUserAgent['version'];
                }
            }
        } else {
            $name          = $browserFromUserAgent['name'];
            $version       = $browserFromUserAgent['version'];
            $short         = $browserFromUserAgent['short_name'];
            $engine        = $browserFromUserAgent['engine'];
            $engineVersion = $browserFromUserAgent['engine_version'];
        }

        $family  = self::getBrowserFamily((string) $short);
        $appHash = $this->browserHints->parse();

        if (null !== $appHash && $name !== $appHash['name']) {
            $name    = $appHash['name'];
            $version = '';
            $short   = self::getBrowserShortName($name);

            if (\preg_match('~Chrome/.+ Safari/537.36~i', $this->userAgent)) {
                $engine        = 'Blink';
                $family        = self::getBrowserFamily((string) $short) ?? 'Chrome';
                $engineVersion = $this->buildEngineVersion($engine);
            }

            if (null === $short) {
                // This Exception should never be thrown. If so a defined browser name is missing in $availableBrowsers
                throw new \Exception(\sprintf(
                    'Detected browser name "%s" was not found in $availableBrowsers. Tried to parse user agent: %s',
                    $name,
                    $this->userAgent
                )); // @codeCoverageIgnore
            }
        }

        if (empty($name)) {
            return [];
        }

        // exclude Blink engine version for browsers
        if ('Blink' === $engine && 'Flow Browser' === $name) {
            $engineVersion = '';
        }

        return [
            'type'           => 'browser',
            'name'           => $name,
            'short_name'     => $short,
            'version'        => $version,
            'engine'         => $engine,
            'engine_version' => $engineVersion,
            'family'         => $family,
        ];
    }

    /**
     * Returns the browser that can be safely detected from client hints
     *
     * @return array
     */
    protected function parseBrowserFromClientHints(): array
    {
        $name = $version = $short = '';

        if ($this->clientHints instanceof ClientHints && $this->clientHints->getBrandList()) {
            $brands = $this->clientHints->getBrandList();

            foreach ($brands as $brand => $brandVersion) {
                $brand = $this->applyClientHintMapping($brand);

                foreach (self::$availableBrowsers as $browserShort => $browserName) {
                    if ($this->fuzzyCompare("{$brand}", $browserName)
                        || $this->fuzzyCompare($brand . ' Browser', $browserName)
                        || $this->fuzzyCompare("{$brand}", $browserName . ' Browser')
                    ) {
                        $name    = $browserName;
                        $short   = $browserShort;
                        $version = $brandVersion;

                        break;
                    }
                }

                // If we detected a brand, that is not chromium, we will use it, otherwise we will look further
                if ('' !== $name && 'Chromium' !== $name) {
                    break;
                }
            }

            $version = $this->clientHints->getBrandVersion() ?: $version;
        }

        return [
            'name'       => $name,
            'short_name' => $short,
            'version'    => $this->buildVersion($version, []),
        ];
    }

    /**
     * Returns the browser that can be detected from useragent
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function parseBrowserFromUserAgent(): array
    {
        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);

            if ($matches) {
                break;
            }
        }

        if (empty($matches) || empty($regex)) {
            return [
                'name'           => '',
                'short_name'     => '',
                'version'        => '',
                'engine'         => '',
                'engine_version' => '',
            ];
        }

        $name         = $this->buildByMatch($regex['name'], $matches);
        $browserShort = self::getBrowserShortName($name);

        if (null !== $browserShort) {
            $version       = $this->buildVersion((string) $regex['version'], $matches);
            $engine        = $this->buildEngine($regex['engine'] ?? [], $version);
            $engineVersion = $this->buildEngineVersion($engine);

            return [
                'name'           => $name,
                'short_name'     => $browserShort,
                'version'        => $version,
                'engine'         => $engine,
                'engine_version' => $engineVersion,
            ];
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
