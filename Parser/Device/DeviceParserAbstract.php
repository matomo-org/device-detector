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
    const DEVICE_TYPE_SMART_SPEAKER        = 11;

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
        'phablet'               => self::DEVICE_TYPE_PHABLET,
        'smart speaker'         => self::DEVICE_TYPE_SMART_SPEAKER,
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
        'AE' => 'Ace',
        'AA' => 'AllCall',
        'AC' => 'Acer',
        'A9' => 'Advan',
        'AD' => 'Advance',
        'A3' => 'AGM',
        'AZ' => 'Ainol',
        'AI' => 'Airness',
        '0A' => 'AIS',
        'AW' => 'Aiwa',
        'AK' => 'Akai',
        '1A' => 'Alba',
        'AL' => 'Alcatel',
        '3A' => 'AllDocube',
        'A2' => 'Allview',
        'A7' => 'Allwinner',
        'A1' => 'Altech UEC',
        'A5' => 'altron',
        'AN' => 'Arnova',
        '2A' => 'Atom',
        'KN' => 'Amazon',
        'AG' => 'AMGOO',
        'AO' => 'Amoi',
        'AP' => 'Apple',
        'AR' => 'Archos',
        'AS' => 'ARRIS',
        'AB' => 'Arian Space',
        'AT' => 'Airties',
        'A6' => 'Ark',
        'A4' => 'Ask',
        'A8' => 'Assistant',
        'A0' => 'ANS',
        'AU' => 'Asus',
        'AH' => 'AVH',
        'AV' => 'Avvio',
        'AX' => 'Audiovox',
        'AY' => 'Axxion',
        'AM' => 'Azumi Mobile',
        'BB' => 'BBK',
        'BE' => 'Becker',
        'B5' => 'Beeline',
        'BI' => 'Bird',
        'BT' => 'Bitel',
        'BG' => 'BGH',
        'BL' => 'Beetel',
        'BP' => 'Blaupunkt',
        'B3' => 'Bluboo',
        'BF' => 'Black Fox',
        'B6' => 'BDF',
        'BM' => 'Bmobile',
        'BN' => 'Barnes & Noble',
        'BO' => 'BangOlufsen',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        'BU' => 'Blu',
        'BD' => 'Bluegood',
        'B2' => 'Blackview',
        'B4' => 'bogo',
        'BW' => 'Boway',
        'BZ' => 'Bezkam',
        'BX' => 'bq',
        'BV' => 'Bravis',
        'BR' => 'Brondi',
        'B1' => 'Bush',
        'CB' => 'CUBOT',
        'CF' => 'Carrefour',
        'CP' => 'Captiva',
        'CS' => 'Casio',
        'R4' => 'Casper',
        'CA' => 'Cat',
        'C9' => 'CAGI',
        'CE' => 'Celkon',
        'CC' => 'ConCorde',
        'C2' => 'Changhong',
        '2C' => 'Ghong',
        'CH' => 'Cherry Mobile',
        '1C' => 'Chuwi',
        'L8' => 'Clarmin',
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
        'C6' => 'Comio',
        'C7' => 'ComTrade Tesla',
        'C8' => 'Concord',
        'CX' => 'Crescent',
        'C4' => 'Cyrus',
        'CV' => 'CVTE',
        'D5' => 'Daewoo',
        'DA' => 'Danew',
        'DT' => 'Datang',
        'D1' => 'Datsun',
        'DE' => 'Denver',
        'DW' => 'DeWalt',
        'DX' => 'DEXP',
        'DS' => 'Desay',
        'DB' => 'Dbtel',
        'DC' => 'DoCoMo',
        'DG' => 'Dialog',
        'DI' => 'Dicam',
        'D4' => 'Digi',
        'D3' => 'Digicel',
        'DD' => 'Digiland',
        'D2' => 'Digma',
        'D6' => 'Divisat',
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
        'E7' => 'Ergo',
        'ED' => 'Energizer',
        'E4' => 'Echo Mobiles',
        'ES' => 'ECS',
        'E6' => 'EE',
        'EI' => 'Ezio',
        'EM' => 'Eks Mobility',
        'EL' => 'Elephone',
        'EG' => 'Elenberg',
        'EP' => 'Easypix',
        'EK' => 'EKO',
        'E1' => 'Energy Sistem',
        'ER' => 'Ericy',
        'EE' => 'Essential',
        'EN' => 'Eton',
        'E2' => 'Essentielb',
        '1E' => 'Etuline',
        'ET' => 'eTouch',
        'EV' => 'Evertek',
        'E3' => 'Evolio',
        'EO' => 'Evolveo',
        'EX' => 'Explay',
        'E0' => 'EvroMedia',
        'E5' => 'Extrem',
        'EZ' => 'Ezze',
        'E8' => 'E-tel',
        'E9' => 'Evercoss',
        'EU' => 'Eurostar',
        'FA' => 'Fairphone',
        'FM' => 'Famoco',
        'FE' => 'Fengxiang',
        'FI' => 'FiGO',
        'FL' => 'Fly',
        'F1' => 'FinePower',
        'FT' => 'Freetel',
        'FR' => 'Forstar',
        'FO' => 'Foxconn',
        'F2' => 'FORME',
        'FN' => 'FNB',
        'FU' => 'Fujitsu',
        'FD' => 'Fondi',
        'GT' => 'G-TiDE',
        'GM' => 'Garmin-Asus',
        'GA' => 'Gateway',
        'GD' => 'Gemini',
        'GN' => 'General Mobile',
        'GE' => 'Geotel',
        'GH' => 'Ghia',
        'GI' => 'Gionee',
        'GG' => 'Gigabyte',
        'GS' => 'Gigaset',
        'GZ' => 'Ginzzu',
        'G4' => 'Globex',
        'GC' => 'GOCLEVER',
        'GL' => 'Goly',
        'GO' => 'Google',
        'G1' => 'GoMobile',
        'GR' => 'Gradiente',
        'GP' => 'Grape',
        'GU' => 'Grundig',
        'HF' => 'Hafury',
        'HA' => 'Haier',
        'HS' => 'Hasee',
        'HE' => 'HannSpree',
        'HI' => 'Hisense',
        'HL' => 'Hi-Level',
        'H2' => 'Highscreen',
        'H1' => 'Hoffmann',
        'HM' => 'Homtom',
        'HO' => 'Hosin',
        'HZ' => 'Hoozo',
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
        'IH' => 'iHunt',
        'IK' => 'iKoMo',
        'IE' => 'iView',
        'IM' => 'i-mate',
        'I1' => 'iOcean',
        'I2' => 'IconBIT',
        'IL' => 'IMO Mobile',
        'I7' => 'iLA',
        'IW' => 'iNew',
        'IP' => 'iPro',
        'IF' => 'Infinix',
        'I0' => 'InFocus',
        'I5' => 'InnJoo',
        'IN' => 'Innostream',
        'IS' => 'Insignia',
        'I4' => 'Inoi',
        'IR' => 'iRola',
        'IU' => 'iRulu',
        'I6' => 'Irbis',
        'II' => 'Inkti',
        'IX' => 'Intex',
        'IO' => 'i-mobile',
        'IQ' => 'INQ',
        'IT' => 'Intek',
        'IV' => 'Inverto',
        'I3' => 'Impression',
        'IZ' => 'iTel',
        'I9' => 'iZotron',
        'JA' => 'JAY-Tech',
        'JI' => 'Jiayu',
        'JO' => 'Jolla',
        'J5' => 'Just5',
        'KL' => 'Kalley',
        'K4' => 'Kaan',
        'K7' => 'Kaiomy',
        'K6' => 'Kanji',
        'KA' => 'Karbonn',
        'K5' => 'KATV1',
        'KD' => 'KDDI',
        'K1' => 'Kiano',
        'KV' => 'Kivi',
        'KI' => 'Kingsun',
        'KC' => 'Kocaso',
        'KG' => 'Kogan',
        'KO' => 'Konka',
        'KM' => 'Komu',
        'KB' => 'Koobee',
        'KT' => 'K-Touch',
        'KH' => 'KT-Tech',
        'KK' => 'Kodak',
        'KP' => 'KOPO',
        'KW' => 'Konrow',
        'KR' => 'Koridy',
        'K2' => 'KRONO',
        'KS' => 'Kempler & Strauss',
        'K3' => 'Keneksi',
        'KU' => 'Kumai',
        'KY' => 'Kyocera',
        'KZ' => 'Kazam',
        'KE' => 'Krüger&Matz',
        'LQ' => 'LAIQ',
        'L2' => 'Landvo',
        'L6' => 'Land Rover',
        'LV' => 'Lava',
        'LA' => 'Lanix',
        'LK' => 'Lark',
        'LC' => 'LCT',
        'L5' => 'Leagoo',
        'LD' => 'Ledstar',
        'L1' => 'LeEco',
        'L4' => 'Lemhoov',
        'LE' => 'Lenovo',
        'LN' => 'Lenco',
        'LT' => 'Leotec',
        'L7' => 'Lephone',
        'LP' => 'Le Pan',
        'LG' => 'LG',
        'LI' => 'Lingwin',
        'LO' => 'Loewe',
        'LM' => 'Logicom',
        'L3' => 'Lexand',
        'LX' => 'Lexibook',
        'LY' => 'LYF',
        'LU' => 'Lumus',
        'MN' => 'M4tel',
        'MJ' => 'Majestic',
        'MA' => 'Manta Multimedia',
        '5M' => 'Mann',
        '2M' => 'Masstel',
        'MW' => 'Maxwest',
        'M0' => 'Maze',
        'MB' => 'Mobistel',
        '0M' => 'Mecool',
        'M3' => 'Mecer',
        'MD' => 'Medion',
        'M2' => 'MEEG',
        'M1' => 'Meizu',
        '3M' => 'Meitu',
        'ME' => 'Metz',
        'MX' => 'MEU',
        'MI' => 'MicroMax',
        'M5' => 'MIXC',
        'MH' => 'Mobiola',
        '4M' => 'Mobicel',
        'M6' => 'Mobiistar',
        'MC' => 'Mediacom',
        'MK' => 'MediaTek',
        'MO' => 'Mio',
        'M7' => 'Miray',
        'MM' => 'Mpman',
        'M4' => 'Modecom',
        'MF' => 'Mofut',
        'MR' => 'Motorola',
        'MV' => 'Movic',
        'MS' => 'Microsoft',
        'M9' => 'MTC',
        'MP' => 'MegaFon',
        'MZ' => 'MSI',
        'MU' => 'Memup',
        'MT' => 'Mitsubishi',
        'ML' => 'MLLED',
        'MQ' => 'M.T.T.',
        'N4' => 'MTN',
        'MY' => 'MyPhone',
        '1M' => 'MYFON',
        'MG' => 'MyWigo',
        'M8' => 'Myria',
        '6M' => 'Mystery',
        'N3' => 'Navon',
        'N7' => 'National',
        'N5' => 'NOA',
        'NE' => 'NEC',
        'NF' => 'Neffos',
        'NA' => 'Netgear',
        'NU' => 'NeuImage',
        'NG' => 'NGM',
        'NZ' => 'NG Optics',
        'N6' => 'Nobby',
        'NO' => 'Nous',
        'NI' => 'Nintendo',
        'N1' => 'Noain',
        'N2' => 'Nextbit',
        'NK' => 'Nokia',
        'NV' => 'Nvidia',
        'NB' => 'Noblex',
        'NM' => 'Nomi',
        'N0' => 'Nuvo',
        'NL' => 'NUU Mobile',
        'NY' => 'NYX Mobile',
        'NN' => 'Nikon',
        'NW' => 'Newgen',
        'NS' => 'NewsMy',
        'NX' => 'Nexian',
        'NT' => 'NextBook',
        'O3' => 'O+',
        'OB' => 'Obi',
        'O1' => 'Odys',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OP' => 'OPPO',
        'OR' => 'Orange',
        'OS' => 'Ordissimo',
        'OT' => 'O2',
        'OK' => 'Ouki',
        'OE' => 'Oukitel',
        'OU' => 'OUYA',
        'OO' => 'Opsson',
        'OV' => 'Overmax',
        'OY' => 'Oysters',
        'OW' => 'öwn',
        'PN' => 'Panacom',
        'PA' => 'Panasonic',
        'PB' => 'PCBOX',
        'PC' => 'PCD',
        'PD' => 'PCD Argentina',
        'PE' => 'PEAQ',
        'PG' => 'Pentagram',
        'PH' => 'Philips',
        'PI' => 'Pioneer',
        'PX' => 'Pixus',
        'PL' => 'Polaroid',
        'P5' => 'Polytron',
        'P9' => 'Primepad',
        'P6' => 'Proline',
        'PM' => 'Palm',
        'PO' => 'phoneOne',
        'PT' => 'Pantech',
        'PY' => 'Ployer',
        'P4' => 'Plum',
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
        'QH' => 'Q-Touch',
        'QM' => 'QMobile',
        'QA' => 'Quantum',
        'QU' => 'Quechua',
        'QO' => 'Qumo',
        'RA' => 'Ramos',
        'RC' => 'RCA Tablets',
        'RB' => 'Readboy',
        'RI' => 'Rikomagic',
        'RN' => 'Rinno',
        'RV' => 'Riviera',
        'RM' => 'RIM',
        'RK' => 'Roku',
        'RO' => 'Rover',
        'R6' => 'RoverPad',
        'RR' => 'Roadrover',
        'R1' => 'Rokit',
        'R3' => 'Rombica',
        'RT' => 'RT Project',
        'RX' => 'Ritmix',
        'R7' => 'Ritzviva',
        'R5' => 'Ross&Moor',
        'R2' => 'R-TV',
        'RU' => 'Runbo',
        'SQ' => 'Santin BiTBiZ',
        'SA' => 'Samsung',
        'S0' => 'Sanei',
        'SD' => 'Sega',
        'SL' => 'Selfix',
        'SE' => 'Sony Ericsson',
        'S1' => 'Sencor',
        'SF' => 'Softbank',
        'SX' => 'SFR',
        'SG' => 'Sagem',
        'SH' => 'Sharp',
        '3S' => 'Shuttle',
        'SI' => 'Siemens',
        'SJ' => 'Silent Circle',
        '1S' => 'Sigma',
        'SN' => 'Sendo',
        'S6' => 'Senseit',
        'EW' => 'Senwa',
        'SW' => 'Sky',
        'SK' => 'Skyworth',
        'SC' => 'Smartfren',
        'SO' => 'Sony',
        'OI' => 'Sonim',
        'SP' => 'Spice',
        '6S' => 'Spectrum',
        '5S' => 'Sunvell',
        'SU' => 'SuperSonic',
        'S5' => 'Supra',
        'SV' => 'Selevision',
        'SY' => 'Sanyo',
        'SM' => 'Symphony',
        '4S' => 'Syrox',
        'SR' => 'Smart',
        'S7' => 'Smartisan',
        'S4' => 'Star',
        'SB' => 'STF Mobile',
        'S8' => 'STK',
        'S9' => 'Savio',
        '2S' => 'Starway',
        'ST' => 'Storex',
        'S2' => 'Stonex',
        'S3' => 'SunVan',
        'SZ' => 'Sumvision',
        'SS' => 'SWISSMOBILITY',
        '10' => 'Simbans',
        'X1' => 'Safaricom',
        'TA' => 'Tesla',
        'T5' => 'TB Touch',
        'TC' => 'TCL',
        'T7' => 'Teclast',
        'TE' => 'Telit',
        'T4' => 'ThL',
        'TH' => 'TiPhone',
        'TB' => 'Tecno Mobile',
        'TP' => 'TechPad',
        'TD' => 'Tesco',
        'TI' => 'TIANYU',
        'TG' => 'Telego',
        'TL' => 'Telefunken',
        'T2' => 'Telenor',
        'TM' => 'T-Mobile',
        'TN' => 'Thomson',
        'TQ' => 'Timovi',
        'TY' => 'Tooky',
        'T1' => 'Tolino',
        'T9' => 'Top House',
        'TO' => 'Toplux',
        'T8' => 'Touchmate',
        'TS' => 'Toshiba',
        'TT' => 'TechnoTrend',
        'T6' => 'TrekStor',
        'T3' => 'Trevi',
        'TU' => 'Tunisie Telecom',
        'TR' => 'Turbo-X',
        '1T' => 'Turbo',
        '11' => 'True',
        'TV' => 'TVC',
        'TX' => 'TechniSat',
        'TZ' => 'teXet',
        'UC' => 'U.S. Cellular',
        'UH' => 'Uhappy',
        'UG' => 'Ugoos',
        'UL' => 'Ulefone',
        'UO' => 'Unnecto',
        'UN' => 'Unowhy',
        'US' => 'Uniscope',
        'UX' => 'Unimax',
        'UM' => 'UMIDIGI',
        'UU' => 'Unonu',
        'UK' => 'UTOK',
        'UA' => 'Umax',
        'UT' => 'UTStarcom',
        'UZ' => 'Unihertz',
        'VA' => 'Vastking',
        'VD' => 'Videocon',
        'VE' => 'Vertu',
        'VN' => 'Venso',
        'V5' => 'Vivax',
        'VI' => 'Vitelcom',
        'V7' => 'Vinga',
        'VK' => 'VK Mobile',
        'VS' => 'ViewSonic',
        'V9' => 'Vsun',
        'V8' => 'Vesta',
        'VT' => 'Vestel',
        'VR' => 'Vernee',
        'V4' => 'Verizon',
        'VL' => 'Verykool',
        'V6' => 'VGO TEL',
        'VV' => 'Vivo',
        'VX' => 'Vertex',
        'V3' => 'Vinsoc',
        'V2' => 'Vonino',
        'VG' => 'Vorago',
        'V1' => 'Voto',
        'VO' => 'Voxtel',
        'VF' => 'Vodafone',
        'VZ' => 'Vizio',
        'VW' => 'Videoweb',
        'VU' => 'Vulcan',
        'WA' => 'Walton',
        'WF' => 'Wileyfox',
        'WN' => 'Wink',
        'WM' => 'Weimei',
        'WE' => 'WellcoM',
        'WY' => 'Wexler',
        'WI' => 'Wiko',
        'WP' => 'Wieppo',
        'WL' => 'Wolder',
        'WG' => 'Wolfgang',
        'WO' => 'Wonu',
        'W1' => 'Woo',
        'WX' => 'Woxter',
        'XV' => 'X-View',
        'XI' => 'Xiaomi',
        'XL' => 'Xiaolajiao',
        'XN' => 'Xion',
        'XO' => 'Xolo',
        'XR' => 'Xoro',
        'YA' => 'Yarvik',
        'YD' => 'Yandex',
        'Y2' => 'Yes',
        'YE' => 'Yezz',
        'Y1' => 'Yu',
        'YU' => 'Yuandao',
        'YS' => 'Yusun',
        'YO' => 'Yota',
        'YT' => 'Ytone',
        'YX' => 'Yxtel',
        'ZE' => 'Zeemi',
        'ZK' => 'Zenek',
        'ZO' => 'Zonda',
        'ZP' => 'Zopo',
        'ZT' => 'ZTE',
        'ZU' => 'Zuum',
        'ZN' => 'Zen',
        'ZY' => 'Zync',
        'ZQ' => 'ZYQ',
        'XT' => 'X-TIGI',
        'XB' => 'NEXBOX',

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
                throw new \Exception("The brand with name '$brand' should be listed in the deviceBrands array. Tried to parse user agent: ".$this->userAgent); // @codeCoverageIgnore
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
