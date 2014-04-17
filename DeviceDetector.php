<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

class DeviceDetector
{
    /**
     * Detectable device types
     * @var array
     */
    public static $deviceTypes = array(
        'desktop',          // 0
        'smartphone',       // 1
        'tablet',           // 2
        'feature phone',    // 3
        'console',          // 4
        'tv',               // 5
        'car browser',      // 6
        'smart display',    // 7
        'camera'            // 8
    );

    /**
     * Known device brands
     *
     * Note: Before using a new brand in on of the regex files, it needs to be added here
     *
     * @var array
     */
    public static $deviceBrands = array(
        'AC' => 'Acer',
        'AI' => 'Airness',
        'AL' => 'Alcatel',
        'AN' => 'Arnova',
        'AO' => 'Amoi',
        'AP' => 'Apple',
        'AR' => 'Archos',
        'AU' => 'Asus',
        'AV' => 'Avvio',
        'AX' => 'Audiovox',
        'BB' => 'BBK',
        'BE' => 'Becker',
        'BI' => 'Bird',
        'BL' => 'Beetel',
        'BM' => 'Bmobile',
        'BN' => 'Barnes & Noble',
        'BO' => 'BangOlufsen',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        'BX' => 'bq',
        'CA' => 'Cat',
        'CH' => 'Cherry Mobile',
        'CK' => 'Cricket',
        'CL' => 'Compal',
        'CN' => 'CnM',
        'CR' => 'CreNova',
        'CT' => 'Capitel',
        'CO' => 'Coolpad',
        'CU' => 'Cube',
        'DE' => 'Denver',
        'DB' => 'Dbtel',
        'DC' => 'DoCoMo',
        'DI' => 'Dicam',
        'DL' => 'Dell',
        'DM' => 'DMM',
        'DP' => 'Dopod',
        'EC' => 'Ericsson',
        'EI' => 'Ezio',
        'ER' => 'Ericy',
        'ET' => 'eTouch',
        'EZ' => 'Ezze',
        'FL' => 'Fly',
        'GD' => 'Gemini',
        'GI' => 'Gionee',
        'GG' => 'Gigabyte',
        'GO' => 'Google',
        'GR' => 'Gradiente',
        'GU' => 'Grundig',
        'HA' => 'Haier',
        'HP' => 'HP',
        'HT' => 'HTC',
        'HU' => 'Huawei',
        'HX' => 'Humax',
        'IA' => 'Ikea',
        'IB' => 'iBall',
        'IK' => 'iKoMo',
        'IM' => 'i-mate',
        'IN' => 'Innostream',
        'IX' => 'Intex',
        'IO' => 'i-mobile',
        'IQ' => 'INQ',
        'IT' => 'Intek',
        'IV' => 'Inverto',
        'JI' => 'Jiayu',
        'JO' => 'Jolla',
        'KA' => 'Karbonn',
        'KD' => 'KDDI',
        'KN' => 'Kindle',
        'KO' => 'Konka',
        'KT' => 'K-Touch',
        'KH' => 'KT-Tech',
        'KY' => 'Kyocera',
        'LA' => 'Lanix',
        'LC' => 'LCT',
        'LE' => 'Lenovo',
        'LG' => 'LG',
        'LO' => 'Loewe',
        'LU' => 'LGUPlus',
        'LX' => 'Lexibook',
        'MA' => 'Manta Multimedia',
        'MB' => 'Mobistel',
        'MD' => 'Medion',
        'ME' => 'Metz',
        'MI' => 'MicroMax',
        'MK' => 'MediaTek',
        'MO' => 'Mio',
        'MR' => 'Motorola',
        'MS' => 'Microsoft',
        'MT' => 'Mitsubishi',
        'MY' => 'MyPhone',
        'NE' => 'NEC',
        'NG' => 'NGM',
        'NI' => 'Nintendo',
        'NK' => 'Nokia',
        'NN' => 'Nikon',
        'NW' => 'Newgen',
        'NX' => 'Nexian',
        'OD' => 'Onda',
        'OP' => 'OPPO',
        'OR' => 'Orange',
        'OT' => 'O2',
        'OU' => 'OUYA',
        'PA' => 'Panasonic',
        'PE' => 'PEAQ',
        'PH' => 'Philips',
        'PL' => 'Polaroid',
        'PM' => 'Palm',
        'PO' => 'phoneOne',
        'PT' => 'Pantech',
        'PP' => 'PolyPad',
        'PR' => 'Prestigio',
        'QT' => 'Qtek',
        'RM' => 'RIM',
        'RO' => 'Rover',
        'SA' => 'Samsung',
        'SD' => 'Sega',
        'SE' => 'Sony Ericsson',
        'SF' => 'Softbank',
        'SG' => 'Sagem',
        'SH' => 'Sharp',
        'SI' => 'Siemens',
        'SN' => 'Sendo',
        'SO' => 'Sony',
        'SP' => 'Spice',
        'SU' => 'SuperSonic',
        'SV' => 'Selevision',
        'SY' => 'Sanyo',
        'SM' => 'Symphony',
        'SR' => 'Smart',
        'TA' => 'Tesla',
        'TC' => 'TCL',
        'TE' => 'Telit',
        'TH' => 'TiPhone',
        'TI' => 'TIANYU',
        'TL' => 'Telefunken',
        'TM' => 'T-Mobile',
        'TN' => 'Thomson',
        'TO' => 'Toplux',
        'TS' => 'Toshiba',
        'TT' => 'TechnoTrend',
        'TV' => 'TVC',
        'TX' => 'TechniSat',
        'TZ' => 'teXet',
        'UT' => 'UTStarcom',
        'VD' => 'Videocon',
        'VE' => 'Vertu',
        'VI' => 'Vitelcom',
        'VK' => 'VK Mobile',
        'VS' => 'ViewSonic',
        'VT' => 'Vestel',
        'VO' => 'Voxtel',
        'VW' => 'Videoweb',
        'WB' => 'Web TV',
        'WE' => 'WellcoM',
        'WO' => 'Wonu',
        'WX' => 'Woxter',
        'XI' => 'Xiaomi',
        'XX' => 'Unknown',
        'YU' => 'Yuandao',
        'ZO' => 'Zonda',
        'ZT' => 'ZTE',
    );

    /**
     * Known operating systems mapped to their internal short codes
     *
     * @var array
     */
    public static $operatingSystems = array(
        'AIX' => 'AIX',
        'AND' => 'Android',
        'AMG' => 'AmigaOS',
        'ATV' => 'Apple TV',
        'ARL' => 'Arch Linux',
        'BTR' => 'BackTrack',
        'SBA' => 'Bada',
        'BEO' => 'BeOS',
        'BLB' => 'BlackBerry OS',
        'QNX' => 'BlackBerry Tablet OS',
        'BMP' => 'Brew',
        'CES' => 'CentOS',
        'COS' => 'Chrome OS',
        'DEB' => 'Debian',
        'DFB' => 'DragonFly',
        'FED' => 'Fedora',
        'FOS' => 'Firefox OS',
        'BSD' => 'FreeBSD',
        'GNT' => 'Gentoo',
        'GTV' => 'Google TV',
        'HPX' => 'HP-UX',
        'HAI' => 'Haiku OS',
        'IRI' => 'IRIX',
        'INF' => 'Inferno',
        'KNO' => 'Knoppix',
        'KBT' => 'Kubuntu',
        'LIN' => 'Linux',
        'LBT' => 'Lubuntu',
        'MAC' => 'Mac',
        'MDR' => 'Mandriva',
        'SMG' => 'MeeGo',
        'MIN' => 'Mint',
        'NBS' => 'NetBSD',
        'WII' => 'Nintendo',
        'NDS' => 'Nintendo Mobile',
        'OS2' => 'OS/2',
        'T64' => 'OSF1',
        'OBS' => 'OpenBSD',
        'PSP' => 'PlayStation Portable',
        'PS3' => 'PlayStation',
        'PRS' => 'Presto',
        'PPY' => 'Puppy',
        'RHT' => 'Red Hat',
        'ROS' => 'RISC OS',
        'SAB' => 'Sabayon',
        'SSE' => 'SUSE',
        'SAF' => 'Sailfish OS',
        'SLW' => 'Slackware',
        'SOS' => 'Solaris',
        'SYL' => 'Syllable',
        'SYM' => 'Symbian',
        'SYS' => 'Symbian OS',
        'S40' => 'Symbian OS Series 40',
        'S60' => 'Symbian OS Series 60',
        'SY3' => 'Symbian^3',
        'TIZ' => 'Tizen',
        'UBT' => 'Ubuntu',
        'WTV' => 'WebTV',
        'WIN' => 'Windows',
        'W2K' => 'Windows 2000',
        'W31' => 'Windows 3.1',
        'WI7' => 'Windows 7',
        'WI8' => 'Windows 8',
        'W95' => 'Windows 95',
        'W98' => 'Windows 98',
        'WCE' => 'Windows CE',
        'WME' => 'Windows ME',
        'WMO' => 'Windows Mobile',
        'WNT' => 'Windows NT',
        'WPH' => 'Windows Phone',
        'WRT' => 'Windows RT',
        'WS3' => 'Windows Server 2003',
        'WVI' => 'Windows Vista',
        'WXP' => 'Windows XP',
        'XBX' => 'Xbox',
        'XBT' => 'Xubuntu',
        'YNS' => 'YunOs',
        'IOS' => 'iOS',
        'POS' => 'palmOS',
        'WOS' => 'webOS'
    );

    /**
     * Operating system families that are known as desktop only
     *
     * @var array
     */
    protected static $desktopOsArray = array('AmigaOS', 'IBM', 'Linux', 'Mac', 'Unix', 'Windows', 'BeOS');

    /**
     * Operating system families mapped to the short codes of the associated operating systems
     *
     * @var array
     */
    public static $osFamilies = array(
        'Android'               => array('AND'),
        'AmigaOS'               => array('AMG'),
        'Apple TV'              => array('ATV'),
        'BlackBerry'            => array('BLB', 'QNX'),
        'Brew'                  => array('BMP'),
        'BeOS'                  => array('BEO', 'HAI'),
        'Chrome OS'             => array('COS'),
        'Firefox OS'            => array('FOS'),
        'Gaming Console'        => array('WII', 'PS3'),
        'Google TV'             => array('GTV'),
        'IBM'                   => array('OS2'),
        'iOS'                   => array('IOS'),
        'RISC OS'               => array('ROS'),
        'Linux'                 => array('LIN', 'ARL', 'DEB', 'KNO', 'MIN', 'UBT', 'KBT', 'XBT', 'LBT', 'FED', 'RHT', 'MDR', 'GNT', 'SAB', 'SLW', 'SSE', 'PPY', 'CES', 'BTR', 'YNS', 'PRS', 'SAF'),
        'Mac'                   => array('MAC'),
        'Mobile Gaming Console' => array('PSP', 'NDS', 'XBX'),
        'Other Mobile'          => array('WOS', 'POS', 'SBA', 'TIZ', 'SMG'),
        'Symbian'               => array('SYM', 'SYS', 'SY3', 'S60', 'S40'),
        'Unix'                  => array('SOS', 'AIX', 'HPX', 'BSD', 'NBS', 'OBS', 'DFB', 'SYL', 'IRI', 'T64', 'INF'),
        'WebTV'                 => array('WTV'),
        'Windows'               => array('WI7', 'WI8', 'WVI', 'WS3', 'WXP', 'W2K', 'WNT', 'WME', 'W98', 'W95', 'WRT', 'W31', 'WIN'),
        'Windows Mobile'        => array('WPH', 'WMO', 'WCE')
    );

    /**
     * Browser families mapped to the short codes of the associated browsers
     *
     * @var array
     */
    public static $browserFamilies = array(
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
        'Sailfish Browser'   => array('SA')
    );

    /**
     * Known browsers mapped to their internal short codes
     *
     * @var array
     */
    public static $browsers = array(
        'AA' => 'Avant Browser',
        'AB' => 'ABrowse',
        'AG' => 'ANTGalio',
        'AM' => 'Amaya',
        'AN' => 'Android Browser',
        'AR' => 'Arora',
        'AV' => 'Amiga Voyager',
        'AW' => 'Amiga Aweb',
        'BB' => 'BlackBerry Browser',
        'BD' => 'Baidu Browser',
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
        'TB' => 'Thunderbird',
        'TZ' => 'Tizen Browser',
        'UC' => 'UC Browser',
        'WE' => 'WebPositive',
        'WO' => 'wOSBrowser',
        'YA' => 'Yandex Browser',
        'XI' => 'Xiino'
    );

    /**
     * Constant used as value for unknown browser / os
     */
    const UNKNOWN = "UNK";


    protected static $regexesDir            = '/regexes/';
    protected static $osRegexesFile         = 'oss.yml';
    protected static $browserRegexesFile    = 'browsers.yml';
    protected static $mobileRegexesFile     = 'mobiles.yml';
    protected static $televisionRegexesFile = 'televisions.yml';
    protected static $botRegexesFile        = 'bots.yml';

    /**
     * Holds the useragent that should be parsed
     * @var string
     */
    protected $userAgent;

    /**
     * Holds the operating system data after parsing the UA
     * @var array
     */
    protected $os = null;

    /**
     * Holds the browser data after parsing the UA
     * @var array
     */
    protected $browser = null;

    /**
     * Holds the device type after parsing the UA
     * @var string
     */
    protected $device = '';

    /**
     * Holds the device brand data after parsing the UA
     * @var string
     */
    protected $brand = '';

    /**
     * Holds the device model data after parsing the UA
     * @var string
     */
    protected $model = '';

    /**
     * Holds bot information if parsing the UA results in a bot
     * (All other information attributes will stay empty in that case)
     *
     * If $discardBotInformation is set to true, this property will be set to
     * true if parsed UA is identified as bot, additional information will be not available
     *
     * @var array|boolean
     */
    protected $bot = null;

    protected $discardBotInformation = false;

    /**
     * Holds the cache class used for caching the parsed yml-Files
     * @var stdClass
     */
    protected $cache = null;

    /**
     * Constructor
     *
     * @param string $userAgent  UA to parse
     */
    public function __construct($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Sets the Cache class
     *
     * Note: The given class needs to have a 'get' and 'set' method to be used
     *
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Sets whether to discard additional bot information
     * If information is discarded it's only possible check whether UA was detected as bot or not.
     * (Discarding information speeds up the detection a bit)
     *
     * @param bool $discard
     */
    public function discardBotInformation($discard=true)
    {
        $this->discardBotInformation = $discard;
    }

    /**
     * Returns if the parsed UA was identified as a Bot
     *
     * @see bots.yml for a list of detected bots
     *
     * @return bool
     */
    public function isBot()
    {
        return !empty($this->bot);
    }

    /**
     * Returns if the parsed UA was identified as a HbbTV device
     *
     * @return bool
     */
    public function isHbbTv()
    {
        $regex = 'HbbTV/([1-9]{1}(\.[0-9]{1}){1,2})';
        return $this->matchUserAgent($regex);
    }

    /**
     * Returns if the parsed UA was identified as a touch enabled device
     *
     * Note: That only applies to windows 8 tablets
     *
     * @return bool
     */
    public function isTouchEnabled()
    {
        $regex = 'Touch';
        return $this->matchUserAgent($regex);
    }

    public function isMobile()
    {
        return !$this->isDesktop();
    }

    /**
     * Returns if the parsed UA was identified as desktop device
     * Desktop devices are all devices with an unknown type that are running a desktop os
     *
     * @see self::$desktopOsArray
     *
     * @return bool
     */
    public function isDesktop()
    {
        $osShort = $this->getOs('short_name');
        if (empty($osShort) || empty(self::$operatingSystems[$osShort])) {
            return false;
        }

        foreach (self::$osFamilies as $family => $familyOs) {
            if (in_array($osShort, $familyOs)) {
                $decodedFamily = $family;
                break;
            }
        }
        return in_array($decodedFamily, self::$desktopOsArray);
    }

    /**
     * Returns the operating system data extracted from the parsed UA
     *
     * If $attr is given only that property will be returned
     *
     * @param string $attr  property to return(optional)
     *
     * @return array|string
     */
    public function getOs($attr = '')
    {
        if ($attr == '') {
            return $this->os;
        }

        if (!isset($this->os[$attr])) {
            return self::UNKNOWN;
        }

        return $this->os[$attr];
    }

    /**
     * Returns the browser data extracted from the parsed UA
     *
     * If $attr is given only that property will be returned
     *
     * @param string $attr  property to return(optional)
     *
     * @return array|string
     */
    public function getBrowser($attr = '')
    {
        if ($attr == '') {
            return $this->browser;
        }

        if (!isset($this->browser[$attr])) {
            return self::UNKNOWN;
        }

        return $this->browser[$attr];
    }

    /**
     * Returns the device type extracted from the parsed UA
     *
     * @see self::$deviceTypes for available device types
     *
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Returns the device brand extracted from the parsed UA
     *
     * @see self::$deviceBrand for available device brands
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Returns the device model extracted from the parsed UA
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Returns the user agent that is set to be parsed
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Returns the bot extracted from the parsed UA
     *
     * @return array
     */
    public function getBot()
    {
        return $this->bot;
    }

    /**
     * Triggers the parsing of the current user agent
     */
    public function parse()
    {
        $this->parseBot();
        if ($this->isBot())
            return;

        $this->parseOs();

        $this->parseBrowser();

        if($this->isHbbTv()) {
            $this->parseTelevision();
        } else {
            $this->parseMobile();
        }

        if (empty($this->device) && $this->isHbbTv()) {
            $this->device = array_search('tv', self::$deviceTypes);
        } else if (empty($this->device) && $this->isDesktop()) {
            $this->device = array_search('desktop', self::$deviceTypes);
        }

        /**
         * Android up to 3.0 was designed for smartphones only. But as 3.0, which was tablet only, was published
         * too late, there were a bunch of tablets running with 2.x
         * With 4.0 the two trees were merged and it is for smartphones and tablets
         *
         * So were are expecting that all devices running Android < 2 are smartphones
         * Devices running Android 3.X are tablets. Device type of Android 2.X and 4.X+ are unknown
         */
        if (empty($this->device) && $this->getOs('short_name') == 'AND' && $this->getOs('version') != '') {
            if (version_compare($this->getOs('version'), '2.0') == -1) {
                $this->device = array_search('smartphone', self::$deviceTypes);
            } else if (version_compare($this->getOs('version'), '3.0') >= 0 AND version_compare($this->getOs('version'), '4.0') == -1) {
                $this->device = array_search('tablet', self::$deviceTypes);
            }
        }

        /**
         * According to http://msdn.microsoft.com/en-us/library/ie/hh920767(v=vs.85).aspx
         * Internet Explorer 10 introduces the "Touch" UA string token. If this token is present at the end of the
         * UA string, the computer has touch capability, and is running Windows 8 (or later).
         * This UA string will be transmitted on a touch-enabled system running Windows 8 (RT)
         *
         * As most touch enabled devices are tablets and only a smaller part are desktops/notebooks we assume that
         * all Windows 8 touch devices are tablets.
         */
        if (empty($this->device) && in_array($this->getOs('short_name'), array('WI8', 'WRT')) && $this->isTouchEnabled()) {
            $this->device = array_search('tablet', self::$deviceTypes);
        }
    }

    protected function getBotRegexes()
    {
        static $regexBot;
        if(empty($regexBot)) {
            $regexBot = $this->getRegexList('bot', self::$botRegexesFile);
        }
        return $regexBot;
    }

    protected function getOsRegexes()
    {
        static $regexOs;
        if(empty($regexOs)) {
            $regexOs = $this->getRegexList('os', self::$osRegexesFile);
        }
        return $regexOs;
    }

    protected function getBrowserRegexes()
    {
        static $regexBrowser;
        if (empty($regexBrowser)) {
            $regexBrowser = $this->getRegexList('browser', self::$browserRegexesFile);
        }
        return $regexBrowser;
    }

    protected function getMobileRegexes()
    {
        static $regexMobile;
        if (empty($regexMobile)) {
            $regexMobile = $this->getRegexList('mobile', self::$mobileRegexesFile);
        }
        return $regexMobile;
    }

    protected function getTelevisionRegexes()
    {
        static $regexTvs;
        if (empty($regexTvs)) {
            $regexTvs = $this->getRegexList('tv', self::$televisionRegexesFile);
        }
        return $regexTvs;
    }


    protected function saveParsedYmlInCache($type, $data)
    {
        if (!empty($this->cache) && method_exists($this->cache, 'set')) {
            $this->cache->set($type, serialize($data));
        }
    }

    protected function getParsedYmlFromCache($type)
    {
        $data = null;
        if (!empty($this->cache) && method_exists($this->cache, 'get')) {
            $data = $this->cache->get($type);
            if (!empty($data)) {
                $data = unserialize($data);
            }
        }
        return $data;
    }

    /**
     * Parses the current UA and checks whether it contains bot information
     *
     * @see bots.yml for list of detected bots
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in bots.yml and try to match every one
     * -> Set the matched data to $bot
     *
     * If $discardBotInformation is set to TRUE, the Step 2 will be skipped
     * $bot will be set to TRUE instead
     *
     * NOTE: Doing the big match before matching every single regex speeds up the detection
     */
    protected function parseBot()
    {
        $botRegexes = $this->getBotRegexes();

        static $overAllMatch;

        if (empty($overAllMatch)) {
            $overAllMatch = $this->getParsedYmlFromCache('bots-all');
        }

        if (empty($overAllMatch)) {
            // reverse all regexes, so we have the generic one first, which already matches most patterns
            $overAllMatch = array_reduce(array_reverse($botRegexes), function($val1, $val2) {
                if (!empty($val1)) {
                    return $val1.'|'.$val2['regex'];
                } else {
                    return $val2['regex'];
                }
            });
            $this->saveParsedYmlInCache('bots-all', $overAllMatch);
        }

        if (!$this->matchUserAgent($overAllMatch)) {
            $this->bot = null;
            return;
        } else if ($this->discardBotInformation) {
            $this->bot = true;
            return;
        }

        foreach ($botRegexes as $botRegex) {
            $matches = $this->matchUserAgent($botRegex['regex']);
            if ($matches)
                break;
        }

        if (!$matches) {
            $this->bot = null;
            return;
        }

        unset($botRegex['regex']);

        $this->bot = $botRegex;
    }

    protected function parseOs()
    {
        foreach ($this->getOsRegexes() as $osRegex) {
            $matches = $this->matchUserAgent($osRegex['regex']);
            if ($matches)
                break;
        }

        if (!$matches)
            return;

        $name  = $this->buildOsName($osRegex['name'], $matches);
        $short = 'UNK';

        foreach (self::$operatingSystems AS $osShort => $osName) {
            if (strtolower($name) == strtolower($osName)) {
                $name  = $osName;
                $short = $osShort;
            }
        }

        $this->os = array(
            'name'       => $name,
            'short_name' => $short,
            'version'    => $this->buildOsVersion($osRegex['version'], $matches)
        );

        if (in_array($this->os['name'], self::$operatingSystems)) {
            $this->os['short_name'] = array_search($this->os['name'], self::$operatingSystems);
        }
    }

    protected function parseBrowser()
    {
        foreach ($this->getBrowserRegexes() as $browserRegex) {
            $matches = $this->matchUserAgent($browserRegex['regex']);
            if ($matches)
                break;
        }

        if (!$matches)
            return;

        $name  = $this->buildBrowserName($browserRegex['name'], $matches);
        $short = 'XX';

        foreach (self::$browsers AS $browserShort => $browserName) {
            if (strtolower($name) == strtolower($browserName)) {
                $name  = $browserName;
                $short = $browserShort;
            }
        }

        $this->browser = array(
            'name'       => $name,
            'short_name' => $short,
            'version'    => $this->buildBrowserVersion($browserRegex['version'], $matches)
        );
    }

    protected function parseMobile()
    {
        $mobileRegexes = $this->getMobileRegexes();
        $this->parseBrand($mobileRegexes);
        $this->parseModel($mobileRegexes);
    }

    protected function parseTelevision()
    {
        $televisionRegexes = $this->getTelevisionRegexes();
        $this->parseBrand($televisionRegexes);
        $this->parseModel($televisionRegexes);
    }

    protected function parseBrand($deviceRegexes)
    {
        foreach ($deviceRegexes as $brand => $mobileRegex) {
            $matches = $this->matchUserAgent($mobileRegex['regex']);
            if ($matches)
                break;
        }

        if (!$matches)
            return;

        $brandId = array_search($brand, self::$deviceBrands);
        if($brandId === false) {
            throw new Exception("The brand with name '$brand' should be listed in the deviceBrands array.");
        }
        $this->brand = $brandId;
        $this->fullName = $brand;

        if (isset($mobileRegex['device'])) {
            $this->device = array_search($mobileRegex['device'], self::$deviceTypes);
        }

        if (isset($mobileRegex['model'])) {
            $this->model = $this->buildModel($mobileRegex['model'], $matches);
        }
    }

    protected function parseModel($deviceRegexes)
    {
        if (empty($this->brand) || !empty($this->model) || empty($deviceRegexes[$this->fullName]['models']))
            return;

        foreach ($deviceRegexes[$this->fullName]['models'] as $modelRegex) {
            $matches = $this->matchUserAgent($modelRegex['regex']);
            if ($matches)
                break;
        }

        if (!$matches) {
            return;
        }

        $this->model = trim($this->buildModel($modelRegex['model'], $matches));

        if (isset($modelRegex['device'])) {
            $this->device = array_search($modelRegex['device'], self::$deviceTypes);
        }
    }

    protected function matchUserAgent($regex)
    {
        $regex = '/(?:^|[^A-Z_-])(?:' . str_replace('/', '\/', $regex) . ')/i';

        if (preg_match($regex, $this->userAgent, $matches)) {
            return $matches;
        }

        return false;
    }

    protected function buildOsName($osName, $matches)
    {
        return $this->buildByMatch($osName, $matches);
    }

    protected function buildOsVersion($osVersion, $matches)
    {
        $osVersion = $this->buildByMatch($osVersion, $matches);

        $osVersion = $this->buildByMatch($osVersion, $matches, '2');

        $osVersion = str_replace('_', '.', $osVersion);

        return $osVersion;
    }

    protected function buildBrowserName($browserName, $matches)
    {
        return $this->buildByMatch($browserName, $matches);
    }

    protected function buildBrowserVersion($browserVersion, $matches)
    {
        $browserVersion = $this->buildByMatch($browserVersion, $matches);

        $browserVersion = $this->buildByMatch($browserVersion, $matches, '2');

        $browserVersion = str_replace('_', '.', $browserVersion);

        return $browserVersion;
    }

    protected function buildModel($model, $matches)
    {
        $model = $this->buildByMatch($model, $matches);

        $model = $this->buildByMatch($model, $matches, '2');

        $model = $this->buildModelExceptions($model);

        $model = str_replace('_', ' ', $model);

        return $model;
    }

    protected function buildModelExceptions($model)
    {
        if ($this->brand == 'O2') {
            $model = preg_replace('/([a-z])([A-Z])/', '$1 $2', $model);
            $model = ucwords(str_replace('_', ' ', $model));
        }

        return $model;
    }

    /**
     * This method is used in this class for processing results of pregmatch
     * results into string containing recognized information.
     *
     * General algorithm:
     * Parsing UserAgent string consists of trying to match it against list of
     * regular expressions for three different information:
     * browser + version,
     * OS + version,
     * device manufacturer + model.
     *
     * After match has been found iteration stops, and results are processed
     * by buildByMatch.
     * As $item we get decoded name (name of browser, name of OS, name of manufacturer).
     * In array $match we recieve preg_match results containing whole string matched at index 0
     * and following matches in further indexes. Desired action now is to concatenate
     * decoded name ($item) with matches found. First step is to append first found match,
     * which is located in index=1 (that's why $nb is 1 by default).
     * In other cases, where whe know that preg_match may return more than 1 result,
     * we call buildByMatch with $nb = 2 or more, depending on what will be returned from
     * regular expression.
     *
     * Example:
     * We are parsing UserAgent of Firefox 20.0 browser.
     * DeviceDetector calls buildBrowserName() and buildBrowserVersion() in order
     * to retrieve those information.
     * In buildBrowserName() we only have one call of buildByMatch, where passed argument
     * is regular expression testing given string for browser name. In this case, we are only
     * interrested in first hit, so no $nb parameter will be set to 1. After finding match, and calling
     * buildByMatch - we will receive just the name of browser.
     *
     * Also after decoding browser we will get list of regular expressions for this browser name
     * testing UserAgent string for version number. Again we iterate over this list, and after finding first
     * occurence - we break loop and proceed to build by match. Since browser regular expressions can
     * contain two hits (major version and minor version) in function buildBrowserVersion() we have
     * two calls to buildByMatch, one without 3rd parameter, and second with $nb set to 2.
     * This way we can retrieve version number, and assign it to object property.
     *
     * In case of mobiles.yml this schema slightly varies, but general idea is the same.
     *
     * @param string $item
     * @param array $matches
     * @param int|string $nb
     * @return string type
     */
    protected function buildByMatch($item, $matches, $nb = '1')
    {
        if (strpos($item, '$' . $nb) === false)
            return $item;

        $replace = isset($matches[$nb]) ? $matches[$nb] : '';
        return trim(str_replace('$' . $nb, $replace, $item));
    }

    /**
     * @param $osLabel
     * @return bool|string If false, "Unknown"
     */
    public static function getOsFamily($osLabel)
    {
        foreach (self::$osFamilies as $family => $labels) {
            if (in_array($osLabel, $labels)) {
                return $family;
            }
        }
        return false;
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

    public static function getOsNameFromId($os, $ver = false)
    {
        $osFullName = self::$operatingSystems[$os];
        if ($osFullName) {
            if (in_array($os, self::$osFamilies['Windows'])) {
                return $osFullName;
            } else {
                return trim($osFullName . " " . $ver);
            }
        }
        return false;
    }

    static public function getInfoFromUserAgent($ua)
    {
        $deviceDetector = new DeviceDetector($ua);
        $deviceDetector->parse();

        $osFamily = $deviceDetector->getOsFamily($deviceDetector->getOs('short_name'));
        $browserFamily = $deviceDetector->getBrowserFamily($deviceDetector->getBrowser('short_name'));
        $device = $deviceDetector->getDevice();

        $deviceName = $device === '' ? '' : DeviceDetector::$deviceTypes[$device];
        $processed = array(
            'user_agent'     => $deviceDetector->getUserAgent(),
            'os'             => array(
                'name'       => $deviceDetector->getOs('name'),
                'short_name' => $deviceDetector->getOs('short_name'),
                'version'    => $deviceDetector->getOs('version'),
            ),
            'browser'        => array(
                'name'       => $deviceDetector->getBrowser('name'),
                'short_name' => $deviceDetector->getBrowser('short_name'),
                'version'    => $deviceDetector->getBrowser('version'),
            ),
            'device'         => array(
                'type'       => $deviceName,
                'brand'      => $deviceDetector->getBrand(),
                'model'      => $deviceDetector->getModel(),
            ),
            'os_family'      => $osFamily !== false ? $osFamily : 'Unknown',
            'browser_family' => $browserFamily !== false ? $browserFamily : 'Unknown',
        );
        return $processed;
    }

    protected function getRegexList($type, $regexesFile)
    {
        $regexList = $this->getParsedYmlFromCache($type);
        if (empty($regexList)) {
            $regexList = Spyc::YAMLLoad(dirname(__FILE__) . self::$regexesDir . $regexesFile);
            $this->saveParsedYmlInCache($type, $regexList);
        }
        return $regexList;
    }

}
