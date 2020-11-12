<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Device;

use DeviceDetector\Parser\AbstractParser;

/**
 * Class AbstractDeviceParser
 *
 * Abstract class for all device parsers
 */
abstract class AbstractDeviceParser extends AbstractParser
{
    /**
     * @var ?int
     */
    protected $deviceType = null;

    /**
     * @var string
     */
    protected $model = '';

    /**
     * @var string
     */
    protected $brand = '';

    public const DEVICE_TYPE_DESKTOP              = 0;
    public const DEVICE_TYPE_SMARTPHONE           = 1;
    public const DEVICE_TYPE_TABLET               = 2;
    public const DEVICE_TYPE_FEATURE_PHONE        = 3;
    public const DEVICE_TYPE_CONSOLE              = 4;
    public const DEVICE_TYPE_TV                   = 5; // including set top boxes, blu-ray players,...
    public const DEVICE_TYPE_CAR_BROWSER          = 6;
    public const DEVICE_TYPE_SMART_DISPLAY        = 7;
    public const DEVICE_TYPE_CAMERA               = 8;
    public const DEVICE_TYPE_PORTABLE_MEDIA_PAYER = 9;
    public const DEVICE_TYPE_PHABLET              = 10;
    public const DEVICE_TYPE_SMART_SPEAKER        = 11;
    public const DEVICE_TYPE_WEARABLE             = 12; // including set watches, headsets

    /**
     * Detectable device types
     *
     * @var array
     */
    protected static $deviceTypes = [
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
        'wearable'              => self::DEVICE_TYPE_WEARABLE,
    ];

    /**
     * Known device brands
     *
     * Note: Before using a new brand in on of the regex files, it needs to be added here
     *
     * @var array
     */
    public static $deviceBrands = [
        '36' => '360',
        '88' => '8848',
        '3Q' => '3Q',
        '4G' => '4Good',
        '41' => 'A1',
        'AE' => 'Ace',
        'AA' => 'AllCall',
        '3A' => 'AllDocube',
        'AC' => 'Acer',
        '00' => 'Accent',
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
        '20' => 'Alcor',
        '4A' => 'Aligator',
        'A2' => 'Allview',
        'A7' => 'Allwinner',
        'A1' => 'Altech UEC',
        'A5' => 'altron',
        '3L' => 'Alfawise',
        'AN' => 'Arnova',
        '7A' => 'Anry',
        '5A' => 'ArmPhone',
        '40' => 'Artel',
        '2A' => 'Atom',
        'KN' => 'Amazon',
        'AG' => 'AMGOO',
        '9A' => 'Amigoo',
        'AO' => 'Amoi',
        '3N' => 'Aoson',
        'AP' => 'Apple',
        'AR' => 'Archos',
        'AS' => 'ARRIS',
        'AB' => 'Arian Space',
        'AT' => 'Airties',
        '6A' => 'AT&T',
        'A6' => 'Ark',
        'A4' => 'Ask',
        '8A' => 'Asano',
        'A8' => 'Assistant',
        'A0' => 'ANS',
        'AU' => 'Asus',
        'AH' => 'AVH',
        'ZA' => 'Avenzo',
        'AV' => 'Avvio',
        'AX' => 'Audiovox',
        'AY' => 'Axxion',
        'AM' => 'Azumi Mobile',
        'BB' => 'BBK',
        '0B' => 'BB Mobile',
        'BE' => 'Becker',
        'B5' => 'Beeline',
        'B0' => 'Beelink',
        'BI' => 'Bird',
        '1B' => 'Billion',
        'BT' => 'Bitel',
        'B8' => 'BIHEE',
        'B7' => 'Bitmore',
        'BG' => 'BGH',
        'BL' => 'Beetel',
        'BP' => 'Blaupunkt',
        'B3' => 'Bluboo',
        'BF' => 'Black Fox',
        'B6' => 'BDF',
        'BM' => 'Bmobile',
        'BN' => 'Barnes & Noble',
        'BO' => 'BangOlufsen',
        'B9' => 'Bobarry',
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
        'CD' => 'Cloudfone',
        'C0' => 'Clout',
        'CK' => 'Cricket',
        'C1' => 'Crosscall',
        'CL' => 'Compal',
        'CN' => 'CnM',
        'CM' => 'Crius Mea',
        'C3' => 'China Mobile',
        'CR' => 'CreNova',
        '0C' => 'Crony',
        'CT' => 'Capitel',
        'CQ' => 'Compaq',
        '3C' => 'Contixo',
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
        'D7' => 'Datawind',
        'D1' => 'Datsun',
        'DE' => 'Denver',
        'DW' => 'DeWalt',
        'DX' => 'DEXP',
        'DS' => 'Desay',
        'DB' => 'Dbtel',
        'DC' => 'DoCoMo',
        'D9' => 'Dolamee',
        'D0' => 'Doopro',
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
        'DF' => 'Doffler',
        'DV' => 'Doov',
        'DP' => 'Dopod',
        'DR' => 'Doro',
        'D8' => 'Droxio',
        'DU' => 'Dune HD',
        'EB' => 'E-Boda',
        '2E' => 'E-Ceros',
        '5E' => '2E',
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
        '4E' => 'Eltex',
        'L0' => 'Element',
        'EG' => 'Elenberg',
        'EP' => 'Easypix',
        'EK' => 'EKO',
        'E1' => 'Energy Sistem',
        '3E' => 'Enot',
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
        'F3' => 'FireFly Mobile',
        'FL' => 'Fly',
        'F1' => 'FinePower',
        'FT' => 'Freetel',
        'F7' => 'Fero',
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
        'G5' => 'Gome',
        'G1' => 'GoMobile',
        'GR' => 'Gradiente',
        'GP' => 'Grape',
        'G6' => 'Gree',
        'G0' => 'Goophone',
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
        'HW' => 'How',
        'HV' => 'Hotwav',
        'HZ' => 'Hoozo',
        'HP' => 'HP',
        'HT' => 'HTC',
        'HD' => 'Huadoo',
        'HU' => 'Huawei',
        'HX' => 'Humax',
        'HY' => 'Hyrican',
        'HN' => 'Hyundai',
        'IG' => 'iGet',
        'IA' => 'Ikea',
        'IB' => 'iBall',
        '3I' => 'i-Cherry',
        'IJ' => 'i-Joy',
        'IC' => 'iDroid',
        'IY' => 'iBerry',
        '7I' => 'iBrit',
        'IH' => 'iHunt',
        'IK' => 'iKoMo',
        'IE' => 'iView',
        '0J' => 'iVooMi',
        'I8' => 'iVA',
        '1I' => 'iMars',
        'IM' => 'i-mate',
        '2I' => 'iLife',
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
        '5I' => 'Iris',
        'II' => 'Inkti',
        'IX' => 'Intex',
        '4I' => 'Invin',
        'IO' => 'i-mobile',
        'IQ' => 'INQ',
        '8Q' => 'IQM',
        'IT' => 'Intek',
        'IV' => 'Inverto',
        'I3' => 'Impression',
        'IZ' => 'iTel',
        '0I' => 'iTruck',
        'I9' => 'iZotron',
        'JA' => 'JAY-Tech',
        'JI' => 'Jiayu',
        'JG' => 'Jinga',
        'JO' => 'Jolla',
        'J5' => 'Just5',
        'JF' => 'JFone',
        'JK' => 'JKL',
        'KL' => 'Kalley',
        '0K' => 'Klipad',
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
        'K9' => 'Kooper',
        'KT' => 'K-Touch',
        'KH' => 'KT-Tech',
        'KK' => 'Kodak',
        'KP' => 'KOPO',
        'KW' => 'Konrow',
        'KR' => 'Koridy',
        'K2' => 'KRONO',
        'KS' => 'Kempler & Strauss',
        'K3' => 'Keneksi',
        'K8' => 'Kuliao',
        'KU' => 'Kumai',
        'KY' => 'Kyocera',
        'KZ' => 'Kazam',
        '1K' => 'Kzen',
        'KE' => 'Krüger&Matz',
        'KX' => 'Kenxinda',
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
        'LZ' => 'Lesia',
        'LP' => 'Le Pan',
        'LG' => 'LG',
        'LI' => 'Lingwin',
        'LO' => 'Loewe',
        'LM' => 'Logicom',
        '1L' => 'Logic',
        'L3' => 'Lexand',
        'LX' => 'Lexibook',
        'LY' => 'LYF',
        'LU' => 'Lumus',
        '0L' => 'Lumigon',
        'L9' => 'Luna',
        'MN' => 'M4tel',
        'XM' => 'Macoox',
        'MJ' => 'Majestic',
        'MA' => 'Manta Multimedia',
        '6X' => 'Maxtron',
        '5M' => 'Mann',
        '2M' => 'Masstel',
        'MW' => 'Maxwest',
        '7M' => 'Maxcom',
        '0D' => 'MAXVI',
        'M0' => 'Maze',
        'MB' => 'Mobistel',
        '9M' => 'Mobo',
        '0M' => 'Mecool',
        'M3' => 'Mecer',
        'MD' => 'Medion',
        'M2' => 'MEEG',
        'M1' => 'Meizu',
        '3M' => 'Meitu',
        'ME' => 'Metz',
        '09' => 'meanIT',
        '0E' => 'Melrose',
        'MX' => 'MEU',
        'MI' => 'MicroMax',
        '8M' => 'Mito',
        '1X' => 'Minix',
        'M5' => 'MIXC',
        '1Z' => 'MiXzo',
        'MH' => 'Mobiola',
        '4M' => 'Mobicel',
        'M6' => 'Mobiistar',
        'MC' => 'Mediacom',
        'MK' => 'MediaTek',
        'MO' => 'Mio',
        'M7' => 'Miray',
        'MM' => 'Mpman',
        'LS' => 'MLS',
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
        '3T' => 'MyTab',
        '1M' => 'MYFON',
        'MG' => 'MyWigo',
        'M8' => 'Myria',
        '6M' => 'Mystery',
        '1R' => 'Multilaser',
        'N3' => 'Navon',
        'N7' => 'National',
        'N5' => 'NOA',
        'NE' => 'NEC',
        '4N' => 'NextTab',
        '5N' => 'Nos',
        '1N' => 'Neomi',
        'NF' => 'Neffos',
        'NA' => 'Netgear',
        'NU' => 'NeuImage',
        'NG' => 'NGM',
        'NZ' => 'NG Optics',
        'N6' => 'Nobby',
        'NO' => 'Nous',
        'NI' => 'Nintendo',
        '0N' => 'Newman',
        'N1' => 'Noain',
        'N2' => 'Nextbit',
        'NK' => 'Nokia',
        'NV' => 'Nvidia',
        'NB' => 'Noblex',
        'NM' => 'Nomi',
        '2N' => 'Nomu',
        'N0' => 'Nuvo',
        'NL' => 'NUU Mobile',
        'NY' => 'NYX Mobile',
        'NN' => 'Nikon',
        'N9' => 'Newland',
        'NW' => 'Newgen',
        'NS' => 'NewsMy',
        'NX' => 'Nexian',
        'N8' => 'NEXON',
        'NT' => 'NextBook',
        'O3' => 'O+',
        'OB' => 'Obi',
        'O1' => 'Odys',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OX' => 'Onix',
        'OH' => 'Openbox',
        'OP' => 'OPPO',
        'O4' => 'ONN',
        'OR' => 'Orange',
        'O5' => 'Orbic',
        'OS' => 'Ordissimo',
        'OT' => 'O2',
        'OK' => 'Ouki',
        'OE' => 'Oukitel',
        'OU' => 'OUYA',
        'OO' => 'Opsson',
        'OV' => 'Overmax',
        '30' => 'Ovvi',
        'OY' => 'Oysters',
        'O6' => 'Oyyu',
        'OW' => 'öwn',
        'O2' => 'Owwo',
        'OZ' => 'OzoneHD',
        'PN' => 'Panacom',
        'PA' => 'Panasonic',
        'PB' => 'PCBOX',
        'PC' => 'PCD',
        'PD' => 'PCD Argentina',
        'PE' => 'PEAQ',
        'PG' => 'Pentagram',
        'PH' => 'Philips',
        '4P' => 'Philco',
        '1P' => 'Phicomm',
        'PI' => 'Pioneer',
        'PX' => 'Pixus',
        '8P' => 'Pixelphone',
        'PL' => 'Polaroid',
        'P5' => 'Polytron',
        'P9' => 'Primepad',
        '2P' => 'Prixton',
        'P6' => 'Proline',
        'PM' => 'Palm',
        '0P' => 'POCO',
        '3P' => 'Positivo BGH',
        'PO' => 'phoneOne',
        '5P' => 'Phonemax',
        'PT' => 'Pantech',
        'PY' => 'Ployer',
        'P4' => 'Plum',
        'P8' => 'PocketBook',
        'PV' => 'Point of View',
        'PP' => 'PolyPad',
        'P2' => 'Pomp',
        'P3' => 'PPTV',
        'PS' => 'Positivo',
        'PR' => 'Prestigio',
        '6P' => 'Primux',
        'P7' => 'Protruly',
        'P1' => 'ProScan',
        'PU' => 'PULID',
        '7P' => 'P-UP',
        'QB' => 'Q.Bell',
        'QI' => 'Qilive',
        'QT' => 'Qtek',
        'QH' => 'Q-Touch',
        'QM' => 'QMobile',
        'QA' => 'Quantum',
        'QU' => 'Quechua',
        'QO' => 'Qumo',
        'RA' => 'Ramos',
        'RE' => 'Realme',
        'R8' => 'RED',
        'R9' => 'Ravoz',
        'RZ' => 'Razer',
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
        'RG' => 'RugGear',
        'RU' => 'Runbo',
        'RY' => 'Ryte',
        'SQ' => 'Santin',
        'SA' => 'Samsung',
        'S0' => 'Sanei',
        'CZ' => 'Schneider',
        'SD' => 'Sega',
        'SL' => 'Selfix',
        'SE' => 'Sony Ericsson',
        '01' => 'Senkatel',
        'S1' => 'Sencor',
        'SF' => 'Softbank',
        'SX' => 'SFR',
        'SG' => 'Sagem',
        'SH' => 'Sharp',
        '7S' => 'Shift Phones',
        '3S' => 'Shuttle',
        'SI' => 'Siemens',
        'SJ' => 'Silent Circle',
        '1S' => 'Sigma',
        'SN' => 'Sendo',
        '0S' => 'SEMP TCL',
        'S6' => 'Senseit',
        'EW' => 'Senwa',
        'SW' => 'Sky',
        'SK' => 'Skyworth',
        'SC' => 'Smartfren',
        'SO' => 'Sony',
        'OI' => 'Sonim',
        'X2' => 'Soundmax',
        '8S' => 'Soyes',
        'SP' => 'Spice',
        '6S' => 'Spectrum',
        '9S' => 'Sugar',
        '5S' => 'Sunvell',
        '0H' => 'Sunstech',
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
        '1W' => 'Swisstone',
        'QS' => 'SQOOL',
        '0W' => 'Swipe',
        '10' => 'Simbans',
        'X1' => 'Safaricom',
        'TA' => 'Tesla',
        'TK' => 'Takara',
        '4T' => 'Tronsmart',
        '5R' => 'Transpeed',
        'T5' => 'TB Touch',
        'TC' => 'TCL',
        'T7' => 'Teclast',
        'TE' => 'Telit',
        '9T' => 'Tetratab',
        'T4' => 'ThL',
        'TH' => 'TiPhone',
        'TB' => 'Tecno Mobile',
        'TP' => 'TechPad',
        'TD' => 'Tesco',
        'T0' => 'TD Systems',
        'TI' => 'TIANYU',
        '2T' => 'Tinai',
        'TG' => 'Telego',
        'TL' => 'Telefunken',
        '2L' => 'Tele2',
        'T2' => 'Telenor',
        'TM' => 'T-Mobile',
        'TN' => 'Thomson',
        '8T' => 'Time2',
        'TQ' => 'Timovi',
        'TY' => 'Tooky',
        '0T' => 'Tone',
        'T1' => 'Tolino',
        'T9' => 'Top House',
        'TO' => 'Toplux',
        '7T' => 'Torex',
        'T8' => 'Touchmate',
        'TS' => 'Toshiba',
        'TT' => 'TechnoTrend',
        'T6' => 'TrekStor',
        'T3' => 'Trevi',
        'TU' => 'Tunisie Telecom',
        'TR' => 'Turbo-X',
        '1T' => 'Turbo',
        '5T' => 'TurboKids',
        '11' => 'True',
        'TV' => 'TVC',
        'TW' => 'TWM',
        '6T' => 'Twoe',
        'TX' => 'TechniSat',
        'TZ' => 'teXet',
        'UC' => 'U.S. Cellular',
        'UH' => 'Uhappy',
        'U1' => 'Uhans',
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
        '0V' => 'Vipro',
        'VI' => 'Vitelcom',
        'V7' => 'Vinga',
        'VK' => 'VK Mobile',
        'VS' => 'ViewSonic',
        'VH' => 'Vsmart',
        'V9' => 'Vsun',
        'V8' => 'Vesta',
        'VT' => 'Vestel',
        'VR' => 'Vernee',
        'V4' => 'Verizon',
        'VL' => 'Verykool',
        'V6' => 'VGO TEL',
        'VV' => 'Vivo',
        '3V' => 'VVETIME',
        'VX' => 'Vertex',
        'V3' => 'Vinsoc',
        'V2' => 'Vonino',
        '1V' => 'Vontar',
        'VG' => 'Vorago',
        '2V' => 'Vorke',
        'V1' => 'Voto',
        'VO' => 'Voxtel',
        'VF' => 'Vodafone',
        'VM' => 'Vodacom',
        'V0' => 'VKworld',
        'VY' => 'Voyo',
        'VZ' => 'Vizio',
        'VW' => 'Videoweb',
        'VU' => 'Vulcan',
        'WA' => 'Walton',
        'WF' => 'Wileyfox',
        'WN' => 'Wink',
        'WM' => 'Weimei',
        'WE' => 'WellcoM',
        'WY' => 'Wexler',
        'W2' => 'Wigor',
        'WI' => 'Wiko',
        'WS' => 'Winds',
        'WP' => 'Wieppo',
        'WL' => 'Wolder',
        'WG' => 'Wolfgang',
        'WO' => 'Wonu',
        'W1' => 'Woo',
        'WX' => 'Woxter',
        'WR' => 'Wortmann',
        'XV' => 'X-View',
        'XI' => 'Xiaomi',
        'XL' => 'Xiaolajiao',
        'XN' => 'Xion',
        'XO' => 'Xolo',
        'XR' => 'Xoro',
        'XG' => 'Xgody',
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
        'ZF' => 'Zfiner',
        'ZO' => 'Zonda',
        'ZI' => 'Zidoo',
        'ZX' => 'Ziox',
        'ZP' => 'Zopo',
        'ZT' => 'ZTE',
        'ZU' => 'Zuum',
        'ZN' => 'Zen',
        'ZY' => 'Zync',
        'ZQ' => 'ZYQ',
        'XS' => 'Xshitou',
        'XT' => 'X-TIGI',
        'XB' => 'NEXBOX',
        'X3' => 'X-BO',

        // legacy brands, might be removed in future versions
        'WB' => 'Web TV',
        'XX' => 'Unknown',
    ];

    /**
     * Returns the device type represented by one of the DEVICE_TYPE_* constants
     *
     * @return int|null
     */
    public function getDeviceType(): ?int
    {
        return $this->deviceType;
    }

    /**
     * Returns available device types
     *
     * @see $deviceTypes
     *
     * @return array
     */
    public static function getAvailableDeviceTypes(): array
    {
        return self::$deviceTypes;
    }

    /**
     * Returns names of all available device types
     *
     * @return array
     */
    public static function getAvailableDeviceTypeNames(): array
    {
        return \array_keys(self::$deviceTypes);
    }

    /**
     * Returns the name of the given device type
     *
     * @param int $deviceType one of the DEVICE_TYPE_* constants
     *
     * @return mixed
     */
    public static function getDeviceName(int $deviceType)
    {
        return \array_search($deviceType, self::$deviceTypes);
    }

    /**
     * Returns the detected device model
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Returns the detected device brand
     *
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * Returns the full brand name for the given short name
     *
     * @param string $brandId short brand name
     *
     * @return string
     */
    public static function getFullName(string $brandId): string
    {
        if (\array_key_exists($brandId, self::$deviceBrands)) {
            return self::$deviceBrands[$brandId];
        }

        return '';
    }

    /**
     * Returns the brand short code for the given name
     *
     * @param string $brand brand name
     *
     * @return string
     *
     * @deprecated since 4.0 - short codes might be removed in next major release
     */
    public static function getShortCode(string $brand): string
    {
        return \array_search($brand, self::$deviceBrands) ?: '';
    }

    /**
     * Sets the useragent to be parsed
     *
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->reset();
        parent::setUserAgent($userAgent);
    }

    /**
     * @inheritdoc
     */
    public function parse(): ?array
    {
        $brand   = '';
        $regexes = $this->getRegexes();

        foreach ($regexes as $brand => $regex) {
            $matches = $this->matchUserAgent($regex['regex']);

            if ($matches) {
                break;
            }
        }

        if (empty($matches)) {
            return null;
        }

        if ('Unknown' !== $brand) {
            if (!\in_array($brand, self::$deviceBrands)) {
                // This Exception should never be thrown. If so a defined brand name is missing in $deviceBrands
                throw new \Exception(\sprintf(
                    "The brand with name '%s' should be listed in deviceBrands array. Tried to parse user agent: %s",
                    $brand,
                    $this->userAgent
                )); // @codeCoverageIgnore
            }

            $this->brand = (string) $brand;
        }

        if (isset($regex['device']) && \array_key_exists($regex['device'], self::$deviceTypes)) {
            $this->deviceType = self::$deviceTypes[$regex['device']];
        }

        $this->model = '';

        if (isset($regex['model'])) {
            $this->model = $this->buildModel($regex['model'], $matches);
        }

        if (isset($regex['models'])) {
            $modelRegex = '';

            foreach ($regex['models'] as $modelRegex) {
                $modelMatches = $this->matchUserAgent($modelRegex['regex']);

                if ($modelMatches) {
                    break;
                }
            }

            if (empty($modelMatches)) {
                return $this->getResult();
            }

            $this->model = $this->buildModel($modelRegex['model'], $modelMatches);

            if (isset($modelRegex['brand']) && \in_array($modelRegex['brand'], self::$deviceBrands)) {
                $this->brand = (string) $modelRegex['brand'];
            }

            if (isset($modelRegex['device']) && \array_key_exists($modelRegex['device'], self::$deviceTypes)) {
                $this->deviceType = self::$deviceTypes[$modelRegex['device']];
            }
        }

        return $this->getResult();
    }

    /**
     * @param string $model
     * @param array  $matches
     *
     * @return string
     */
    protected function buildModel(string $model, array $matches): string
    {
        $model = $this->buildByMatch($model, $matches);

        $model = \str_replace('_', ' ', $model);

        $model = \preg_replace('/ TD$/i', '', $model);

        if ('Build' === $model || empty($model)) {
            return '';
        }

        return \trim($model);
    }

    /**
     * Resets the stored values
     */
    protected function reset(): void
    {
        $this->deviceType = null;
        $this->model      = '';
        $this->brand      = '';
    }

    /**
     * @return array
     */
    protected function getResult(): array
    {
        return [
            'deviceType' => $this->deviceType,
            'model'      => $this->model,
            'brand'      => $this->brand,
        ];
    }
}
