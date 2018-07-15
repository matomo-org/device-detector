<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Device;

use DeviceDetector\Parser\ParserAbstract;

/**
 * Class DeviceParserAbstract
 *
 * Abstract class for all device parsers
 *
 * @package DeviceDetector\Parser\Device
 */
abstract class DeviceParserAbstract extends ParserAbstract
{
    protected $deviceType = null;
    protected $model = null;
    protected $brand = null;

    const DEVICE_TYPE_DESKTOP              = 0;
    const DEVICE_TYPE_SMARTPHONE           = 1;
    const DEVICE_TYPE_TABLET               = 2;
    const DEVICE_TYPE_FEATURE_PHONE        = 3;
    const DEVICE_TYPE_CONSOLE              = 4;
    const DEVICE_TYPE_TV                   = 5; // including set top boxes, blu-ray players,...
    const DEVICE_TYPE_CAR_BROWSER          = 6;
    const DEVICE_TYPE_SMART_DISPLAY        = 7;
    const DEVICE_TYPE_CAMERA               = 8;
    const DEVICE_TYPE_PORTABLE_MEDIA_PAYER = 9;
    const DEVICE_TYPE_PHABLET              = 10;

    /**
     * Detectable device types
     *
     * @var array
     */
    protected static $deviceTypes = array(
        'desktop'               => self::DEVICE_TYPE_DESKTOP,
        'smartphone'            => self::DEVICE_TYPE_SMARTPHONE,
        'tablet'                => self::DEVICE_TYPE_TABLET,
        'feature phone'         => self::DEVICE_TYPE_FEATURE_PHONE,
        'console'               => self::DEVICE_TYPE_CONSOLE,
        'tv'                    => self::DEVICE_TYPE_TV,
        'car browser'           => self::DEVICE_TYPE_CAR_BROWSER,
        'smart display'         => self::DEVICE_TYPE_SMART_DISPLAY,
        'camera'                => self::DEVICE_TYPE_CAMERA,
        'portable media player' => self::DEVICE_TYPE_PORTABLE_MEDIA_PAYER,
        'phablet'               => self::DEVICE_TYPE_PHABLET
    );

    /**
     * Known device brands
     *
     * Note: Before using a new brand in on of the regex files, it needs to be added here
     *
     * @var array
     */
    public static $deviceBrands = array(
        '3Q' => '3Q',
        '4G' => '4Good',
        'AC' => 'Acer',
        'AZ' => 'Ainol',
        'AI' => 'Airness',
        'AW' => 'Aiwa',
        'AL' => 'Alcatel',
        'A2' => 'Allview',
        'A1' => 'Altech UEC',
        'AN' => 'Arnova',
        'KN' => 'Amazon',
        'AO' => 'Amoi',
        'AP' => 'Apple',
        'AR' => 'Archos',
        'AS' => 'ARRIS',
        'AT' => 'Airties',
        'AU' => 'Asus',
        'AV' => 'Avvio',
        'AX' => 'Audiovox',
        'AY' => 'Axxion',
        'AM' => 'Azumi Mobile',
        'BB' => 'BBK',
        'BE' => 'Becker',
        'BI' => 'Bird',
        'BT' => 'Bitel',
        'BG' => 'BGH',
        'BL' => 'Beetel',
        'BP' => 'Blaupunkt',
        'BM' => 'Bmobile',
        'BN' => 'Barnes & Noble',
        'BO' => 'BangOlufsen',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        'BU' => 'Blu',
        'B2' => 'Blackview',
        'BW' => 'Boway',
        'BX' => 'bq',
        'BV' => 'Bravis',
        'BR' => 'Brondi',
        'B1' => 'Bush',
        'CB' => 'CUBOT',
        'CF' => 'Carrefour',
        'CP' => 'Captiva',
        'CS' => 'Casio',
        'CA' => 'Cat',
        'CE' => 'Celkon',
        'CC' => 'ConCorde',
        'C2' => 'Changhong',
        'CH' => 'Cherry Mobile',
        'CK' => 'Cricket',
        'C1' => 'Crosscall',
        'CL' => 'Compal',
        'CN' => 'CnM',
        'CM' => 'Crius Mea',
        'C3' => 'China Mobile',
        'CR' => 'CreNova',
        'CT' => 'Capitel',
        'CQ' => 'Compaq',
        'CO' => 'Coolpad',
        'C5' => 'Condor',
        'CW' => 'Cowon',
        'CU' => 'Cube',
        'CY' => 'Coby Kyros',
        'C4' => 'Cyrus',
        'DA' => 'Danew',
        'DT' => 'Datang',
        'DE' => 'Denver',
        'DX' => 'DEXP',
        'DS' => 'Desay',
        'DB' => 'Dbtel',
        'DC' => 'DoCoMo',
        'DI' => 'Dicam',
        'D2' => 'Digma',
        'DL' => 'Dell',
        'DN' => 'DNS',
        'DM' => 'DMM',
        'DO' => 'Doogee',
        'DV' => 'Doov',
        'DP' => 'Dopod',
        'DR' => 'Doro',
        'DU' => 'Dune HD',
        'EB' => 'E-Boda',
        'EA' => 'EBEST',
        'EC' => 'Ericsson',
        'ES' => 'ECS',
        'EI' => 'Ezio',
        'EL' => 'Elephone',
        'EP' => 'Easypix',
        'EK' => 'EKO',
        'E1' => 'Energy Sistem',
        'ER' => 'Ericy',
        'EE' => 'Essential',
        'EN' => 'Eton',
        'ET' => 'eTouch',
        'EV' => 'Evertek',
        'EO' => 'Evolveo',
        'EX' => 'Explay',
        'EZ' => 'Ezze',
        'FA' => 'Fairphone',
        'FL' => 'Fly',
        'FT' => 'Freetel',
        'FO' => 'Foxconn',
        'FU' => 'Fujitsu',
        'GM' => 'Garmin-Asus',
        'GA' => 'Gateway',
        'GD' => 'Gemini',
        'GI' => 'Gionee',
        'GG' => 'Gigabyte',
        'GS' => 'Gigaset',
        'GC' => 'GOCLEVER',
        'GL' => 'Goly',
        'GO' => 'Google',
        'GR' => 'Gradiente',
        'GU' => 'Grundig',
        'HA' => 'Haier',
        'HS' => 'Hasee',
        'HE' => 'HannSpree',
        'HI' => 'Hisense',
        'HL' => 'Hi-Level',
        'HM' => 'Homtom',
        'HO' => 'Hosin',
        'HP' => 'HP',
        'HT' => 'HTC',
        'HU' => 'Huawei',
        'HX' => 'Humax',
        'HY' => 'Hyrican',
        'HN' => 'Hyundai',
        'IA' => 'Ikea',
        'IB' => 'iBall',
        'IJ' => 'i-Joy',
        'IY' => 'iBerry',
        'IK' => 'iKoMo',
        'IM' => 'i-mate',
        'I1' => 'iOcean',
        'I2' => 'IconBIT',
        'IW' => 'iNew',
        'IF' => 'Infinix',
        'IN' => 'Innostream',
        'II' => 'Inkti',
        'IX' => 'Intex',
        'IO' => 'i-mobile',
        'IQ' => 'INQ',
        'IT' => 'Intek',
        'IV' => 'Inverto',
        'IZ' => 'iTel',
        'JA' => 'JAY-Tech',
        'JI' => 'Jiayu',
        'JO' => 'Jolla',
        'KA' => 'Karbonn',
        'KD' => 'KDDI',
        'K1' => 'Kiano',
        'KI' => 'Kingsun',
        'KG' => 'Kogan',
        'KO' => 'Konka',
        'KM' => 'Komu',
        'KB' => 'Koobee',
        'KT' => 'K-Touch',
        'KH' => 'KT-Tech',
        'KP' => 'KOPO',
        'KW' => 'Konrow',
        'KR' => 'Koridy',
        'KU' => 'Kumai',
        'KY' => 'Kyocera',
        'KZ' => 'Kazam',
        'L2' => 'Landvo',
        'LV' => 'Lava',
        'LA' => 'Lanix',
        'LC' => 'LCT',
        'L1' => 'LeEco',
        'LE' => 'Lenovo',
        'LN' => 'Lenco',
        'LP' => 'Le Pan',
        'LG' => 'LG',
        'LI' => 'Lingwin',
        'LO' => 'Loewe',
        'LM' => 'Logicom',
        'L3' => 'Lexand',
        'LX' => 'Lexibook',
        'LY' => 'LYF',
        'MJ' => 'Majestic',
        'MA' => 'Manta Multimedia',
        'MB' => 'Mobistel',
        'M3' => 'Mecer',
        'MD' => 'Medion',
        'M2' => 'MEEG',
        'M1' => 'Meizu',
        'ME' => 'Metz',
        'MX' => 'MEU',
        'MI' => 'MicroMax',
        'M5' => 'MIXC',
        'M6' => 'Mobiistar',
        'MC' => 'Mediacom',
        'MK' => 'MediaTek',
        'MO' => 'Mio',
        'MM' => 'Mpman',
        'M4' => 'Modecom',
        'MF' => 'Mofut',
        'MR' => 'Motorola',
        'MS' => 'Microsoft',
        'MZ' => 'MSI',
        'MU' => 'Memup',
        'MT' => 'Mitsubishi',
        'ML' => 'MLLED',
        'MQ' => 'M.T.T.',
        'MY' => 'MyPhone',
        'NE' => 'NEC',
        'NF' => 'Neffos',
        'NA' => 'Netgear',
        'NG' => 'NGM',
        'NO' => 'Nous',
        'NI' => 'Nintendo',
        'N1' => 'Noain',
        'NK' => 'Nokia',
        'NV' => 'Nvidia',
        'NB' => 'Noblex',
        'NM' => 'Nomi',
        'NN' => 'Nikon',
        'NW' => 'Newgen',
        'NX' => 'Nexian',
        'NT' => 'NextBook',
        'OB' => 'Obi',
        'O1' => 'Odys',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OP' => 'OPPO',
        'OR' => 'Orange',
        'OT' => 'O2',
        'OK' => 'Ouki',
        'OU' => 'OUYA',
        'OO' => 'Opsson',
        'OV' => 'Overmax',
        'OY' => 'Oysters',
        'PA' => 'Panasonic',
        'PE' => 'PEAQ',
        'PG' => 'Pentagram',
        'PH' => 'Philips',
        'PI' => 'Pioneer',
        'PL' => 'Polaroid',
        'PM' => 'Palm',
        'PO' => 'phoneOne',
        'PT' => 'Pantech',
        'PY' => 'Ployer',
        'PV' => 'Point of View',
        'PP' => 'PolyPad',
        'P2' => 'Pomp',
        'P3' => 'PPTV',
        'PS' => 'Positivo',
        'PR' => 'Prestigio',
        'P1' => 'ProScan',
        'PU' => 'PULID',
        'QI' => 'Qilive',
        'QT' => 'Qtek',
        'QM' => 'QMobile',
        'QU' => 'Quechua',
        'RA' => 'Ramos',
        'RC' => 'RCA Tablets',
        'RB' => 'Readboy',
        'RI' => 'Rikomagic',
        'RM' => 'RIM',
        'RK' => 'Roku',
        'RO' => 'Rover',
        'SA' => 'Samsung',
        'SD' => 'Sega',
        'SE' => 'Sony Ericsson',
        'S1' => 'Sencor',
        'SF' => 'Softbank',
        'SX' => 'SFR',
        'SG' => 'Sagem',
        'SH' => 'Sharp',
        'SI' => 'Siemens',
        'SN' => 'Sendo',
        'S6' => 'Senseit',
        'SK' => 'Skyworth',
        'SC' => 'Smartfren',
        'SO' => 'Sony',
        'SP' => 'Spice',
        'SU' => 'SuperSonic',
        'S5' => 'Supra',
        'SV' => 'Selevision',
        'SY' => 'Sanyo',
        'SM' => 'Symphony',
        'SR' => 'Smart',
        'S7' => 'Smartisan',
        'S4' => 'Star',
        'S8' => 'STK',
        'ST' => 'Storex',
        'S2' => 'Stonex',
        'S3' => 'SunVan',
        'SZ' => 'Sumvision',
        'TA' => 'Tesla',
        'T5' => 'TB Touch',
        'TC' => 'TCL',
        'T7' => 'Teclast',
        'TE' => 'Telit',
        'T4' => 'ThL',
        'TH' => 'TiPhone',
        'TB' => 'Tecno Mobile',
        'TD' => 'Tesco',
        'TI' => 'TIANYU',
        'TL' => 'Telefunken',
        'T2' => 'Telenor',
        'TM' => 'T-Mobile',
        'TN' => 'Thomson',
        'T1' => 'Tolino',
        'TO' => 'Toplux',
        'TS' => 'Toshiba',
        'TT' => 'TechnoTrend',
        'T6' => 'TrekStor',
        'T3' => 'Trevi',
        'TU' => 'Tunisie Telecom',
        'TR' => 'Turbo-X',
        'TV' => 'TVC',
        'TX' => 'TechniSat',
        'TZ' => 'teXet',
        'UL' => 'Ulefone',
        'UN' => 'Unowhy',
        'US' => 'Uniscope',
        'UM' => 'UMIDIGI',
        'UT' => 'UTStarcom',
        'VA' => 'Vastking',
        'VD' => 'Videocon',
        'VE' => 'Vertu',
        'VI' => 'Vitelcom',
        'VK' => 'VK Mobile',
        'VS' => 'ViewSonic',
        'VT' => 'Vestel',
        'VR' => 'Vernee',
        'VL' => 'Verykool',
        'VV' => 'Vivo',
        'V2' => 'Vonino',
        'V1' => 'Voto',
        'VO' => 'Voxtel',
        'VF' => 'Vodafone',
        'VZ' => 'Vizio',
        'VW' => 'Videoweb',
        'WA' => 'Walton',
        'WF' => 'Wileyfox',
        'WE' => 'WellcoM',
        'WY' => 'Wexler',
        'WI' => 'Wiko',
        'WL' => 'Wolder',
        'WG' => 'Wolfgang',
        'WO' => 'Wonu',
        'WX' => 'Woxter',
        'XI' => 'Xiaomi',
        'XO' => 'Xolo',
        'YA' => 'Yarvik',
        'YU' => 'Yuandao',
        'YS' => 'Yusun',
        'YT' => 'Ytone',
        'ZE' => 'Zeemi',
        'ZO' => 'Zonda',
        'ZP' => 'Zopo',
        'ZT' => 'ZTE',
        'ZN' => 'Zen',

        // legacy brands, might be removed in future versions
        'WB' => 'Web TV',
        'XX' => 'Unknown'
    );

    public function getDeviceType()
    {
        return $this->deviceType;
    }

    /**
     * Returns available device types
     *
     * @see $deviceTypes
     * @return array
     */
    public static function getAvailableDeviceTypes()
    {
        return self::$deviceTypes;
    }

    /**
     * Returns names of all available device types
     *
     * @return array
     */
    public static function getAvailableDeviceTypeNames()
    {
        return array_keys(self::$deviceTypes);
    }

    /**
     * Returns the name of the given device type
     *
     * @param int $deviceType one of the DEVICE_TYPE_* constants
     *
     * @return mixed
     */
    public static function getDeviceName($deviceType)
    {
        return array_search($deviceType, self::$deviceTypes);
    }

    /**
     * Returns the detected device model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Returns the detected device brand
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Returns the full brand name for the given short name
     *
     * @param string $brandId  short brand name
     * @return string
     */
    public static function getFullName($brandId)
    {
        if (array_key_exists($brandId, self::$deviceBrands)) {
            return self::$deviceBrands[$brandId];
        }

        return '';
    }

    /**
     * Sets the useragent to be parsed
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->reset();
        parent::setUserAgent($userAgent);
    }

    public function parse()
    {
        $regexes = $this->getRegexes();
        foreach ($regexes as $brand => $regex) {
            $matches = $this->matchUserAgent($regex['regex']);
            if ($matches) {
                break;
            }
        }

        if (empty($matches)) {
            return false;
        }

        if ($brand != 'Unknown') {
            $brandId = array_search($brand, self::$deviceBrands);
            if ($brandId === false) {
                // This Exception should never be thrown. If so a defined brand name is missing in $deviceBrands
                throw new \Exception("The brand with name '$brand' should be listed in the deviceBrands array."); // @codeCoverageIgnore
            }
            $this->brand = $brandId;
        }

        if (isset($regex['device']) && in_array($regex['device'], self::$deviceTypes)) {
            $this->deviceType = self::$deviceTypes[$regex['device']];
        }

        $this->model = '';
        if (isset($regex['model'])) {
            $this->model = $this->buildModel($regex['model'], $matches);
        }

        if (isset($regex['models'])) {
            foreach ($regex['models'] as $modelRegex) {
                $modelMatches = $this->matchUserAgent($modelRegex['regex']);
                if ($modelMatches) {
                    break;
                }
            }

            if (empty($modelMatches)) {
                return true;
            }

            $this->model = trim($this->buildModel($modelRegex['model'], $modelMatches));

            if (isset($modelRegex['brand']) && $brandId = array_search($modelRegex['brand'], self::$deviceBrands)) {
                $this->brand = $brandId;
            }

            if (isset($modelRegex['device']) && in_array($modelRegex['device'], self::$deviceTypes)) {
                $this->deviceType = self::$deviceTypes[$modelRegex['device']];
            }
        }

        return true;
    }

    protected function buildModel($model, $matches)
    {
        $model = $this->buildByMatch($model, $matches);

        $model = str_replace('_', ' ', $model);

        $model = preg_replace('/ TD$/i', '', $model);

        if ($model === 'Build') {
            return null;
        }

        return $model;
    }

    protected function reset()
    {
        $this->deviceType = null;
        $this->model      = null;
        $this->brand      = null;
    }
}
