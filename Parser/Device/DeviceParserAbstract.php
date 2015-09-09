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
        'AC' => 'Acer',
        'AZ' => 'Ainol',
        'AI' => 'Airness',
        'AL' => 'Alcatel',
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
        'BB' => 'BBK',
        'BE' => 'Becker',
        'BI' => 'Bird',
        'BL' => 'Beetel',
        'BM' => 'Bmobile',
        'BN' => 'Barnes & Noble',
        'BO' => 'BangOlufsen',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        'BU' => 'Blu',
        'BX' => 'bq',
        'BR' => 'Brondi',
        'CB' => 'CUBOT',
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
        'CR' => 'CreNova',
        'CT' => 'Capitel',
        'CQ' => 'Compaq',
        'CO' => 'Coolpad',
        'CW' => 'Cowon',
        'CU' => 'Cube',
        'CY' => 'Coby Kyros',
        'DA' => 'Danew',
        'DE' => 'Denver',
        'DB' => 'Dbtel',
        'DC' => 'DoCoMo',
        'DI' => 'Dicam',
        'DL' => 'Dell',
        'DM' => 'DMM',
        'DO' => 'Doogee',
        'DV' => 'Doov',
        'DP' => 'Dopod',
        'DU' => 'Dune HD',
        'EB' => 'E-Boda',
        'EC' => 'Ericsson',
        'ES' => 'ECS',
        'EI' => 'Ezio',
        'EL' => 'Elephone',
        'EP' => 'Easypix',
        'ER' => 'Ericy',
        'ET' => 'eTouch',
        'EV' => 'Evertek',
        'EZ' => 'Ezze',
        'FL' => 'Fly',
        'FU' => 'Fujitsu',
        'GM' => 'Garmin-Asus',
        'GA' => 'Gateway',
        'GD' => 'Gemini',
        'GI' => 'Gionee',
        'GG' => 'Gigabyte',
        'GS' => 'Gigaset',
        'GO' => 'Google',
        'GR' => 'Gradiente',
        'GU' => 'Grundig',
        'HA' => 'Haier',
        'HI' => 'Hisense',
        'HL' => 'Hi-Level',
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
        'IF' => 'Infinix',
        'IN' => 'Innostream',
        'II' => 'Inkti',
        'IX' => 'Intex',
        'IO' => 'i-mobile',
        'IQ' => 'INQ',
        'IT' => 'Intek',
        'IV' => 'Inverto',
        'IZ' => 'iTel',
        'JI' => 'Jiayu',
        'JO' => 'Jolla',
        'KA' => 'Karbonn',
        'KD' => 'KDDI',
        'KO' => 'Konka',
        'KM' => 'Komu',
        'KT' => 'K-Touch',
        'KH' => 'KT-Tech',
        'KY' => 'Kyocera',
        'KZ' => 'Kazam',
        'LV' => 'Lava',
        'LA' => 'Lanix',
        'LC' => 'LCT',
        'LE' => 'Lenovo',
        'LN' => 'Lenco',
        'LP' => 'Le Pan',
        'LG' => 'LG',
        'LO' => 'Loewe',
        'LM' => 'Logicom',
        'LX' => 'Lexibook',
        'MA' => 'Manta Multimedia',
        'MB' => 'Mobistel',
        'MD' => 'Medion',
        'M1' => 'Meizu',
        'ME' => 'Metz',
        'MX' => 'MEU',
        'MI' => 'MicroMax',
        'MC' => 'Mediacom',
        'MK' => 'MediaTek',
        'MO' => 'Mio',
        'MM' => 'Mpman',
        'MR' => 'Motorola',
        'MS' => 'Microsoft',
        'MZ' => 'MSI',
        'MU' => 'Memup',
        'MT' => 'Mitsubishi',
        'ML' => 'MLLED',
        'MQ' => 'M.T.T.',
        'MY' => 'MyPhone',
        'NE' => 'NEC',
        'NG' => 'NGM',
        'NI' => 'Nintendo',
        'NK' => 'Nokia',
        'NN' => 'Nikon',
        'NW' => 'Newgen',
        'NX' => 'Nexian',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OP' => 'OPPO',
        'OR' => 'Orange',
        'OT' => 'O2',
        'OU' => 'OUYA',
        'OO' => 'Opsson',
        'PA' => 'Panasonic',
        'PE' => 'PEAQ',
        'PH' => 'Philips',
        'PL' => 'Polaroid',
        'PM' => 'Palm',
        'PO' => 'phoneOne',
        'PT' => 'Pantech',
        'PP' => 'PolyPad',
        'PS' => 'Positivo',
        'PR' => 'Prestigio',
        'PU' => 'PULID',
        'QI' => 'Qilive',
        'QT' => 'Qtek',
        'QU' => 'Quechua',
        'OV' => 'Overmax',
        'OY' => 'Oysters',
        'RA' => 'Ramos',
        'RI' => 'Rikomagic',
        'RM' => 'RIM',
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
        'SK' => 'Skyworth',
        'SC' => 'Smartfren',
        'SO' => 'Sony',
        'SP' => 'Spice',
        'SU' => 'SuperSonic',
        'SV' => 'Selevision',
        'SY' => 'Sanyo',
        'SM' => 'Symphony',
        'SR' => 'Smart',
        'ST' => 'Storex',
        'S2' => 'Stonex',
        'SZ' => 'Sumvision',
        'TA' => 'Tesla',
        'TC' => 'TCL',
        'TE' => 'Telit',
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
        'T3' => 'Trevi',
        'TU' => 'Tunisie Telecom',
        'TR' => 'Turbo-X',
        'TV' => 'TVC',
        'TX' => 'TechniSat',
        'TZ' => 'teXet',
        'UN' => 'Unowhy',
        'UT' => 'UTStarcom',
        'VD' => 'Videocon',
        'VE' => 'Vertu',
        'VI' => 'Vitelcom',
        'VK' => 'VK Mobile',
        'VS' => 'ViewSonic',
        'VT' => 'Vestel',
        'VV' => 'Vivo',
        'VO' => 'Voxtel',
        'VF' => 'Vodafone',
        'VW' => 'Videoweb',
        'WA' => 'Walton',
        'WB' => 'Web TV',
        'WE' => 'WellcoM',
        'WY' => 'Wexler',
        'WI' => 'Wiko',
        'WL' => 'Wolder',
        'WO' => 'Wonu',
        'WX' => 'Woxter',
        'XI' => 'Xiaomi',
        'XO' => 'Xolo',
        'XX' => 'Unknown',
        'YA' => 'Yarvik',
        'YU' => 'Yuandao',
        'YS' => 'Yusun',
        'ZO' => 'Zonda',
        'ZP' => 'Zopo',
        'ZT' => 'ZTE',
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

        $brandId = array_search($brand, self::$deviceBrands);
        if ($brandId === false) {
            // This Exception should never be thrown. If so a defined brand name is missing in $deviceBrands
            throw new \Exception("The brand with name '$brand' should be listed in the deviceBrands array."); // @codeCoverageIgnore
        }
        $this->brand = $brandId;

        if (isset($regex['device']) && in_array($regex['device'], self::$deviceTypes)) {
            $this->deviceType = self::$deviceTypes[$regex['device']];
        }

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

        return $model;
    }
}
