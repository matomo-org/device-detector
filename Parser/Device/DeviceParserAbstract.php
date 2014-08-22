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

    const DEVICE_TYPE_DESKTOP       = 0;
    const DEVICE_TYPE_SMARTPHONE    = 1;
    const DEVICE_TYPE_TABLET        = 2;
    const DEVICE_TYPE_FEATURE_PHONE = 3;
    const DEVICE_TYPE_CONSOLE       = 4;
    const DEVICE_TYPE_TV            = 5;
    const DEVICE_TYPE_CAR_BROWSER   = 6;
    const DEVICE_TYPE_SMART_DISPLAY = 7;
    const DEVICE_TYPE_CAMERA        = 8;

    /**
     * Detectable device types
     * @var array
     */
    protected static $deviceTypes = array(
        'desktop'       => self::DEVICE_TYPE_DESKTOP,
        'smartphone'    => self::DEVICE_TYPE_SMARTPHONE,
        'tablet'        => self::DEVICE_TYPE_TABLET,
        'feature phone' => self::DEVICE_TYPE_FEATURE_PHONE,
        'console'       => self::DEVICE_TYPE_CONSOLE,
        'tv'            => self::DEVICE_TYPE_TV,
        'car browser'   => self::DEVICE_TYPE_CAR_BROWSER,
        'smart display' => self::DEVICE_TYPE_SMART_DISPLAY,
        'camera'        => self::DEVICE_TYPE_CAMERA
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
        'BU' => 'Blu',
        'BX' => 'bq',
        'CA' => 'Cat',
        'CC' => 'ConCorde',
        'CH' => 'Cherry Mobile',
        'CK' => 'Cricket',
        'CL' => 'Compal',
        'CN' => 'CnM',
        'CR' => 'CreNova',
        'CT' => 'Capitel',
        'CO' => 'Coolpad',
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
        'DP' => 'Dopod',
        'EB' => 'E-Boda',
        'EC' => 'Ericsson',
        'EI' => 'Ezio',
        'EP' => 'Easypix',
        'ER' => 'Ericy',
        'ET' => 'eTouch',
        'EV' => 'Evertek',
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
        'JI' => 'Jiayu',
        'JO' => 'Jolla',
        'KA' => 'Karbonn',
        'KD' => 'KDDI',
        'KN' => 'Kindle',
        'KO' => 'Konka',
        'KT' => 'K-Touch',
        'KH' => 'KT-Tech',
        'KY' => 'Kyocera',
        'KZ' => 'Kazam',
        'LA' => 'Lanix',
        'LC' => 'LCT',
        'LE' => 'Lenovo',
        'LN' => 'Lenco',
        'LG' => 'LG',
        'LO' => 'Loewe',
        'LM' => 'Logicom',
        'LU' => 'LGUPlus',
        'LX' => 'Lexibook',
        'MA' => 'Manta Multimedia',
        'MB' => 'Mobistel',
        'MD' => 'Medion',
        'ME' => 'Metz',
        'MI' => 'MicroMax',
        'MK' => 'MediaTek',
        'MO' => 'Mio',
        'MM' => 'Mpman',
        'MR' => 'Motorola',
        'MS' => 'Microsoft',
        'MU' => 'Memup',
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
        'OO' => 'Opsson',
        'PA' => 'Panasonic',
        'PE' => 'PEAQ',
        'PH' => 'Philips',
        'PL' => 'Polaroid',
        'PM' => 'Palm',
        'PO' => 'phoneOne',
        'PT' => 'Pantech',
        'PP' => 'PolyPad',
        'PR' => 'Prestigio',
        'QI' => 'Qilive',
        'QT' => 'Qtek',
        'QU' => 'Quechua',
        'OY' => 'Oysters',
        'RA' => 'Ramos',
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
        'ST' => 'Storex',
        'SZ' => 'Sumvision',
        'TA' => 'Tesla',
        'TC' => 'TCL',
        'TE' => 'Telit',
        'TH' => 'TiPhone',
        'TB' => 'Tecno Mobile',
        'TI' => 'TIANYU',
        'TL' => 'Telefunken',
        'TM' => 'T-Mobile',
        'TN' => 'Thomson',
        'T1' => 'Tolino',
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
        'WI' => 'Wiko',
        'WL' => 'Wolder',
        'WO' => 'Wonu',
        'WX' => 'Woxter',
        'XI' => 'Xiaomi',
        'XX' => 'Unknown',
        'YU' => 'Yuandao',
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
        if($brandId === false) {
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
                if ($modelMatches)
                    break;
            }

            if (empty($modelMatches)) {
                return true;
            }

            $this->model = trim($this->buildModel($modelRegex['model'], $modelMatches));

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