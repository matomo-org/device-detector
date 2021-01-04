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
    public const DEVICE_TYPE_PERIPHERAL           = 13; // including portable terminal, portable projector

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
        'peripheral'            => self::DEVICE_TYPE_PERIPHERAL,
    ];

    /**
     * Known device brands
     *
     * Note: Before using a new brand in on of the regex files, it needs to be added here
     *
     * @var array
     */
    public static $deviceBrands = [
        '5E' => '2E',
        '36' => '360',
        '3Q' => '3Q',
        '4G' => '4Good',
        '04' => '4ife',
        '88' => '8848',
        '41' => 'A1',
        'A3' => 'AGM',
        '0A' => 'AIS',
        'AG' => 'AMGOO',
        'A0' => 'ANS',
        'AS' => 'ARRIS',
        '6A' => 'AT&T',
        'AH' => 'AVH',
        '00' => 'Accent',
        'AE' => 'Ace',
        'AC' => 'Acer',
        'A9' => 'Advan',
        'AD' => 'Advance',
        'AZ' => 'Ainol',
        'AI' => 'Airness',
        'AT' => 'Airties',
        'AW' => 'Aiwa',
        'AK' => 'Akai',
        '1A' => 'Alba',
        'AL' => 'Alcatel',
        '20' => 'Alcor',
        '3L' => 'Alfawise',
        '4A' => 'Aligator',
        'AA' => 'AllCall',
        '3A' => 'AllDocube',
        'A2' => 'Allview',
        'A7' => 'Allwinner',
        'A1' => 'Altech UEC',
        '66' => 'Altice',
        'KN' => 'Amazon',
        '9A' => 'Amigoo',
        'AO' => 'Amoi',
        '60' => 'Andowl',
        '7A' => 'Anry',
        '3N' => 'Aoson',
        'AP' => 'Apple',
        'AR' => 'Archos',
        'AB' => 'Arian Space',
        'A6' => 'Ark',
        '5A' => 'ArmPhone',
        'AN' => 'Arnova',
        '40' => 'Artel',
        '21' => 'Artizlee',
        '8A' => 'Asano',
        '90' => 'Asanzo',
        'A4' => 'Ask',
        'A8' => 'Assistant',
        'AU' => 'Asus',
        '2A' => 'Atom',
        'AX' => 'Audiovox',
        'ZA' => 'Avenzo',
        'AV' => 'Avvio',
        'AY' => 'Axxion',
        'AM' => 'Azumi Mobile',
        '0B' => 'BB Mobile',
        'BB' => 'BBK',
        'B6' => 'BDF',
        'BG' => 'BGH',
        'B8' => 'BIHEE',
        'BO' => 'BangOlufsen',
        'BN' => 'Barnes & Noble',
        'BE' => 'Becker',
        'B5' => 'Beeline',
        'B0' => 'Beelink',
        'BL' => 'Beetel',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        'BZ' => 'Bezkam',
        '6B' => 'Bigben',
        '1B' => 'Billion',
        'BH' => 'BioRugged',
        'BI' => 'Bird',
        'BT' => 'Bitel',
        'B7' => 'Bitmore',
        'BK' => 'Bkav',
        '5B' => 'Black Bear',
        'BF' => 'Black Fox',
        'B2' => 'Blackview',
        'BP' => 'Blaupunkt',
        'BU' => 'Blu',
        'B3' => 'Bluboo',
        '2B' => 'Bluedot',
        'BD' => 'Bluegood',
        'LB' => 'Bluewave',
        'BM' => 'Bmobile',
        'B9' => 'Bobarry',
        'BW' => 'Boway',
        '8B' => 'Brandt',
        'BV' => 'Bravis',
        'BR' => 'Brondi',
        'B1' => 'Bush',
        'C9' => 'CAGI',
        'CB' => 'CUBOT',
        'CV' => 'CVTE',
        'CT' => 'Capitel',
        'CP' => 'Captiva',
        'CF' => 'Carrefour',
        'CS' => 'Casio',
        'R4' => 'Casper',
        'CA' => 'Cat',
        'CE' => 'Celkon',
        'C2' => 'Changhong',
        'CH' => 'Cherry Mobile',
        'C3' => 'China Mobile',
        '1C' => 'Chuwi',
        'L8' => 'Clarmin',
        'CD' => 'Cloudfone',
        '6C' => 'Cloudpad',
        'C0' => 'Clout',
        'CN' => 'CnM',
        'CY' => 'Coby Kyros',
        'C7' => 'ComTrade Tesla',
        'C6' => 'Comio',
        'CL' => 'Compal',
        'CQ' => 'Compaq',
        'CC' => 'ConCorde',
        'C8' => 'Concord',
        'C5' => 'Condor',
        '4C' => 'Conquest',
        '3C' => 'Contixo',
        'CO' => 'Coolpad',
        'CW' => 'Cowon',
        'CR' => 'CreNova',
        'CX' => 'Crescent',
        'CK' => 'Cricket',
        'CM' => 'Crius Mea',
        '0C' => 'Crony',
        'C1' => 'Crosscall',
        'CU' => 'Cube',
        'C4' => 'Cyrus',
        'DX' => 'DEXP',
        'DM' => 'DMM',
        'DN' => 'DNS',
        'D5' => 'Daewoo',
        'DA' => 'Danew',
        'DT' => 'Datang',
        'D7' => 'Datawind',
        'D1' => 'Datsun',
        'DB' => 'Dbtel',
        'DW' => 'DeWalt',
        'DL' => 'Dell',
        'DE' => 'Denver',
        'DS' => 'Desay',
        'DG' => 'Dialog',
        'DI' => 'Dicam',
        'D4' => 'Digi',
        'D3' => 'Digicel',
        'DD' => 'Digiland',
        'D2' => 'Digma',
        '1D' => 'Diva',
        'D6' => 'Divisat',
        'DC' => 'DoCoMo',
        'DF' => 'Doffler',
        'D9' => 'Dolamee',
        'DO' => 'Doogee',
        'D0' => 'Doopro',
        'DV' => 'Doov',
        'DP' => 'Dopod',
        'DR' => 'Doro',
        'D8' => 'Droxio',
        'DU' => 'Dune HD',
        'EB' => 'E-Boda',
        '2E' => 'E-Ceros',
        'E8' => 'E-tel',
        'EA' => 'EBEST',
        'ES' => 'ECS',
        'E6' => 'EE',
        'EK' => 'EKO',
        '7E' => 'ELARI',
        'EH' => 'EXO',
        'EP' => 'Easypix',
        'E4' => 'Echo Mobiles',
        'EM' => 'Eks Mobility',
        'L0' => 'Element',
        'EG' => 'Elenberg',
        'EL' => 'Elephone',
        '4E' => 'Eltex',
        'ED' => 'Energizer',
        'E1' => 'Energy Sistem',
        '3E' => 'Enot',
        '8E' => 'Epik One',
        'E7' => 'Ergo',
        'EC' => 'Ericsson',
        'ER' => 'Ericy',
        'EE' => 'Essential',
        'E2' => 'Essentielb',
        'EN' => 'Eton',
        '1E' => 'Etuline',
        'EU' => 'Eurostar',
        'E9' => 'Evercoss',
        'EV' => 'Evertek',
        'E3' => 'Evolio',
        'EO' => 'Evolveo',
        'E0' => 'EvroMedia',
        'XE' => 'ExMobile',
        'EX' => 'Explay',
        'E5' => 'Extrem',
        'EI' => 'Ezio',
        'EZ' => 'Ezze',
        'FN' => 'FNB',
        'F2' => 'FORME',
        'FA' => 'Fairphone',
        'FM' => 'Famoco',
        'FE' => 'Fengxiang',
        'F7' => 'Fero',
        'FI' => 'FiGO',
        'F1' => 'FinePower',
        'F3' => 'FireFly Mobile',
        'FL' => 'Fly',
        'FD' => 'Fondi',
        'F0' => 'Fonos',
        'FR' => 'Forstar',
        'RF' => 'Fortis',
        'FO' => 'Foxconn',
        'FT' => 'Freetel',
        'FU' => 'Fujitsu',
        'GT' => 'G-TiDE',
        'G2' => 'GEOFOX',
        'GC' => 'GOCLEVER',
        'GM' => 'Garmin-Asus',
        'GA' => 'Gateway',
        'GD' => 'Gemini',
        'GN' => 'General Mobile',
        'GE' => 'Geotel',
        'GH' => 'Ghia',
        '2C' => 'Ghong',
        'GG' => 'Gigabyte',
        'GS' => 'Gigaset',
        'GZ' => 'Ginzzu',
        'GI' => 'Gionee',
        'G4' => 'Globex',
        'G1' => 'GoMobile',
        'GL' => 'Goly',
        'G5' => 'Gome',
        'GO' => 'Google',
        'G0' => 'Goophone',
        'GR' => 'Gradiente',
        'GP' => 'Grape',
        'G6' => 'Gree',
        'GU' => 'Grundig',
        'HP' => 'HP',
        'HT' => 'HTC',
        'HF' => 'Hafury',
        'HA' => 'Haier',
        'HE' => 'HannSpree',
        'HK' => 'Hardkernel',
        'HS' => 'Hasee',
        'H6' => 'Helio',
        'ZH' => 'Hezire',
        'HL' => 'Hi-Level',
        'H2' => 'Highscreen',
        '1H' => 'Hipstreet',
        'HI' => 'Hisense',
        'H1' => 'Hoffmann',
        'H0' => 'Hometech',
        'HM' => 'Homtom',
        'HZ' => 'Hoozo',
        'HO' => 'Hosin',
        'HV' => 'Hotwav',
        'HW' => 'How',
        'HD' => 'Huadoo',
        'HU' => 'Huawei',
        'HX' => 'Humax',
        'HR' => 'Hurricane',
        'HY' => 'Hyrican',
        'HN' => 'Hyundai',
        'IL' => 'IMO Mobile',
        'IQ' => 'INQ',
        '8Q' => 'IQM',
        'I2' => 'IconBIT',
        'IA' => 'Ikea',
        'I3' => 'Impression',
        'I0' => 'InFocus',
        '6I' => 'Inco',
        'IF' => 'Infinix',
        'II' => 'Inkti',
        'I5' => 'InnJoo',
        'IN' => 'Innostream',
        'I4' => 'Inoi',
        'IS' => 'Insignia',
        'IT' => 'Intek',
        'IX' => 'Intex',
        'IV' => 'Inverto',
        '4I' => 'Invin',
        'I6' => 'Irbis',
        '5I' => 'Iris',
        'JA' => 'JAY-Tech',
        'JF' => 'JFone',
        'JK' => 'JKL',
        'JI' => 'Jiayu',
        'JG' => 'Jinga',
        'JO' => 'Jolla',
        'J5' => 'Just5',
        'KT' => 'K-Touch',
        'K5' => 'KATV1',
        'KD' => 'KDDI',
        'KP' => 'KOPO',
        'K2' => 'KRONO',
        'KH' => 'KT-Tech',
        'K4' => 'Kaan',
        'K7' => 'Kaiomy',
        'KL' => 'Kalley',
        'K6' => 'Kanji',
        'KA' => 'Karbonn',
        'KZ' => 'Kazam',
        'KS' => 'Kempler & Strauss',
        'K3' => 'Keneksi',
        'KX' => 'Kenxinda',
        'K1' => 'Kiano',
        'KI' => 'Kingsun',
        'KV' => 'Kivi',
        '0K' => 'Klipad',
        'KC' => 'Kocaso',
        'KK' => 'Kodak',
        'KG' => 'Kogan',
        'KM' => 'Komu',
        'KO' => 'Konka',
        'KW' => 'Konrow',
        'KB' => 'Koobee',
        '7K' => 'Koolnee',
        'K9' => 'Kooper',
        'KR' => 'Koridy',
        'KE' => 'Krüger&Matz',
        'K8' => 'Kuliao',
        '8K' => 'Kult',
        'KU' => 'Kumai',
        'KY' => 'Kyocera',
        '1K' => 'Kzen',
        'LQ' => 'LAIQ',
        'LC' => 'LCT',
        'LG' => 'LG',
        'LY' => 'LYF',
        'L6' => 'Land Rover',
        'L2' => 'Landvo',
        'LA' => 'Lanix',
        'LK' => 'Lark',
        'LV' => 'Lava',
        'LP' => 'Le Pan',
        'L1' => 'LeEco',
        'L5' => 'Leagoo',
        'LD' => 'Ledstar',
        '4B' => 'Leff',
        'L4' => 'Lemhoov',
        'LN' => 'Lenco',
        'LE' => 'Lenovo',
        'LT' => 'Leotec',
        'L7' => 'Lephone',
        'LZ' => 'Lesia',
        'L3' => 'Lexand',
        'LX' => 'Lexibook',
        'LI' => 'Lingwin',
        'LO' => 'Loewe',
        '1L' => 'Logic',
        'LM' => 'Logicom',
        '0L' => 'Lumigon',
        'LU' => 'Lumus',
        'L9' => 'Luna',
        'MQ' => 'M.T.T.',
        'MN' => 'M4tel',
        '0D' => 'MAXVI',
        'M2' => 'MEEG',
        'MX' => 'MEU',
        'M5' => 'MIXC',
        'ML' => 'MLLED',
        'LS' => 'MLS',
        'MZ' => 'MSI',
        'M9' => 'MTC',
        'N4' => 'MTN',
        '1M' => 'MYFON',
        'XM' => 'Macoox',
        'MJ' => 'Majestic',
        '5M' => 'Mann',
        'MA' => 'Manta Multimedia',
        '2M' => 'Masstel',
        '50' => 'Matrix',
        '7M' => 'Maxcom',
        '6X' => 'Maxtron',
        'MW' => 'Maxwest',
        'M0' => 'Maze',
        'M3' => 'Mecer',
        '0M' => 'Mecool',
        'MK' => 'MediaTek',
        'MC' => 'Mediacom',
        'MD' => 'Medion',
        'MP' => 'MegaFon',
        '3M' => 'Meitu',
        'M1' => 'Meizu',
        '0E' => 'Melrose',
        'MU' => 'Memup',
        'ME' => 'Metz',
        '1Z' => 'MiXzo',
        'MI' => 'MicroMax',
        'MS' => 'Microsoft',
        '1X' => 'Minix',
        'OM' => 'Mintt',
        'MO' => 'Mio',
        'M7' => 'Miray',
        '8M' => 'Mito',
        'MT' => 'Mitsubishi',
        '6W' => 'MobiWire',
        '4M' => 'Mobicel',
        'M6' => 'Mobiistar',
        'MH' => 'Mobiola',
        'MB' => 'Mobistel',
        '9M' => 'Mobo',
        'M4' => 'Modecom',
        'MF' => 'Mofut',
        'MR' => 'Motorola',
        'MV' => 'Movic',
        'MM' => 'Mpman',
        '1R' => 'Multilaser',
        'MY' => 'MyPhone',
        '3T' => 'MyTab',
        'MG' => 'MyWigo',
        'M8' => 'Myria',
        '6M' => 'Mystery',
        'NE' => 'NEC',
        'XB' => 'NEXBOX',
        'N8' => 'NEXON',
        'NZ' => 'NG Optics',
        'NG' => 'NGM',
        'N5' => 'NOA',
        'NL' => 'NUU Mobile',
        'NY' => 'NYX Mobile',
        'N7' => 'National',
        '6N' => 'Navitech',
        'N3' => 'Navon',
        'NF' => 'Neffos',
        '1N' => 'Neomi',
        'NA' => 'Netgear',
        'NU' => 'NeuImage',
        'NW' => 'Newgen',
        'N9' => 'Newland',
        '0N' => 'Newman',
        'NS' => 'NewsMy',
        'NX' => 'Nexian',
        'NT' => 'NextBook',
        '4N' => 'NextTab',
        'N2' => 'Nextbit',
        'NN' => 'Nikon',
        'NI' => 'Nintendo',
        'N1' => 'Noain',
        'N6' => 'Nobby',
        'NB' => 'Noblex',
        'NK' => 'Nokia',
        'NM' => 'Nomi',
        '2N' => 'Nomu',
        '7N' => 'NorthTech',
        '5N' => 'Nos',
        'NO' => 'Nous',
        'NJ' => 'NuAns',
        'N0' => 'Nuvo',
        'NV' => 'Nvidia',
        'O3' => 'O+',
        'OT' => 'O2',
        'O4' => 'ONN',
        'OP' => 'OPPO',
        'OU' => 'OUYA',
        'O7' => 'Oale',
        'OB' => 'Obi',
        'O1' => 'Odys',
        'OA' => 'Okapia',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OX' => 'Onix',
        '2O' => 'OpelMobile',
        'OH' => 'Openbox',
        'OO' => 'Opsson',
        'OR' => 'Orange',
        'O5' => 'Orbic',
        'OS' => 'Ordissimo',
        'OK' => 'Ouki',
        'OE' => 'Oukitel',
        'OV' => 'Overmax',
        '30' => 'Ovvi',
        'O2' => 'Owwo',
        'OY' => 'Oysters',
        'O6' => 'Oyyu',
        'OZ' => 'OzoneHD',
        '7P' => 'P-UP',
        'PB' => 'PCBOX',
        'PC' => 'PCD',
        'PD' => 'PCD Argentina',
        'PE' => 'PEAQ',
        '0P' => 'POCO',
        'P3' => 'PPTV',
        'PU' => 'PULID',
        'PM' => 'Palm',
        'PN' => 'Panacom',
        'PA' => 'Panasonic',
        'PT' => 'Pantech',
        'PG' => 'Pentagram',
        '1P' => 'Phicomm',
        '4P' => 'Philco',
        'PH' => 'Philips',
        '5P' => 'Phonemax',
        'PI' => 'Pioneer',
        '8P' => 'Pixelphone',
        'PX' => 'Pixus',
        '9P' => 'Planet Computers',
        'PY' => 'Ployer',
        'P4' => 'Plum',
        'P8' => 'PocketBook',
        'PV' => 'Point of View',
        'PL' => 'Polaroid',
        'PP' => 'PolyPad',
        'P5' => 'Polytron',
        'P2' => 'Pomp',
        'P0' => 'Poppox',
        'PS' => 'Positivo',
        '3P' => 'Positivo BGH',
        'FP' => 'Premio',
        'PR' => 'Prestigio',
        'P9' => 'Primepad',
        '6P' => 'Primux',
        '2P' => 'Prixton',
        'P1' => 'ProScan',
        'P6' => 'Proline',
        'P7' => 'Protruly',
        'QH' => 'Q-Touch',
        'QB' => 'Q.Bell',
        'QM' => 'QMobile',
        'QI' => 'Qilive',
        'QT' => 'Qtek',
        'QA' => 'Quantum',
        'QU' => 'Quechua',
        'QO' => 'Qumo',
        'R2' => 'R-TV',
        'RC' => 'RCA Tablets',
        'R8' => 'RED',
        'RM' => 'RIM',
        'RT' => 'RT Project',
        'RA' => 'Ramos',
        'R9' => 'Ravoz',
        'RZ' => 'Razer',
        '2R' => 'Reach',
        'RB' => 'Readboy',
        'RE' => 'Realme',
        'RD' => 'Reeder',
        'RI' => 'Rikomagic',
        'RN' => 'Rinno',
        'RX' => 'Ritmix',
        'R7' => 'Ritzviva',
        'RV' => 'Riviera',
        'RR' => 'Roadrover',
        'R1' => 'Rokit',
        'RK' => 'Roku',
        'R3' => 'Rombica',
        'R5' => 'Ross&Moor',
        'RO' => 'Rover',
        'R6' => 'RoverPad',
        'RG' => 'RugGear',
        'RU' => 'Runbo',
        'RY' => 'Ryte',
        '0S' => 'SEMP TCL',
        'SX' => 'SFR',
        '80' => 'SMARTEC',
        'FS' => 'SPC',
        'QS' => 'SQOOL',
        'SB' => 'STF Mobile',
        'S8' => 'STK',
        'SS' => 'SWISSMOBILITY',
        'X1' => 'Safaricom',
        'SG' => 'Sagem',
        'SA' => 'Samsung',
        'S0' => 'Sanei',
        '12' => 'Sansui',
        'SQ' => 'Santin',
        'SY' => 'Sanyo',
        'S9' => 'Savio',
        'CZ' => 'Schneider',
        'SD' => 'Sega',
        '9G' => 'Selenga',
        'SV' => 'Selevision',
        'SL' => 'Selfix',
        'S1' => 'Sencor',
        'SN' => 'Sendo',
        '01' => 'Senkatel',
        'S6' => 'Senseit',
        'EW' => 'Senwa',
        'SH' => 'Sharp',
        '7S' => 'Shift Phones',
        'RS' => 'Shtrikh-M',
        '3S' => 'Shuttle',
        '13' => 'Sico',
        'SI' => 'Siemens',
        '1S' => 'Sigma',
        '70' => 'Silelis',
        'SJ' => 'Silent Circle',
        '10' => 'Simbans',
        '31' => 'Siragon',
        'SW' => 'Sky',
        'SK' => 'Skyworth',
        '14' => 'Smadl',
        'SR' => 'Smart',
        'SC' => 'Smartfren',
        'S7' => 'Smartisan',
        'SF' => 'Softbank',
        'OI' => 'Sonim',
        'SO' => 'Sony',
        'SE' => 'Sony Ericsson',
        'X2' => 'Soundmax',
        '8S' => 'Soyes',
        '6S' => 'Spectrum',
        'SP' => 'Spice',
        'S4' => 'Star',
        'OL' => 'Starlight',
        '18' => 'Starmobile',
        '2S' => 'Starway',
        'S2' => 'Stonex',
        'ST' => 'Storex',
        '9S' => 'Sugar',
        'SZ' => 'Sumvision',
        'S3' => 'SunVan',
        '0H' => 'Sunstech',
        '5S' => 'Sunvell',
        'SU' => 'SuperSonic',
        'S5' => 'Supra',
        '0W' => 'Swipe',
        '1W' => 'Swisstone',
        'SM' => 'Symphony',
        '4S' => 'Syrox',
        'TM' => 'T-Mobile',
        'T5' => 'TB Touch',
        'TC' => 'TCL',
        'T0' => 'TD Systems',
        'TI' => 'TIANYU',
        '5C' => 'TTEC',
        'TV' => 'TVC',
        'TW' => 'TWM',
        'TK' => 'Takara',
        '9N' => 'Tanix',
        'TP' => 'TechPad',
        'TX' => 'TechniSat',
        'TT' => 'TechnoTrend',
        'T7' => 'Teclast',
        'TB' => 'Tecno Mobile',
        '2L' => 'Tele2',
        'TL' => 'Telefunken',
        'TG' => 'Telego',
        'T2' => 'Telenor',
        'TE' => 'Telit',
        'TD' => 'Tesco',
        'TA' => 'Tesla',
        '9T' => 'Tetratab',
        'T4' => 'ThL',
        'TN' => 'Thomson',
        'O0' => 'Thuraya',
        'TH' => 'TiPhone',
        '8T' => 'Time2',
        'TQ' => 'Timovi',
        '2T' => 'Tinai',
        'TF' => 'Tinmo',
        'T1' => 'Tolino',
        '0T' => 'Tone',
        'TY' => 'Tooky',
        'T9' => 'Top House',
        'TO' => 'Toplux',
        '7T' => 'Torex',
        'TS' => 'Toshiba',
        'T8' => 'Touchmate',
        '5R' => 'Transpeed',
        'T6' => 'TrekStor',
        'T3' => 'Trevi',
        'TJ' => 'Trifone',
        '4T' => 'Tronsmart',
        '11' => 'True',
        'TU' => 'Tunisie Telecom',
        '1T' => 'Turbo',
        'TR' => 'Turbo-X',
        '5T' => 'TurboKids',
        '6T' => 'Twoe',
        'UC' => 'U.S. Cellular',
        'UM' => 'UMIDIGI',
        'U2' => 'UNIWA',
        'UK' => 'UTOK',
        'UT' => 'UTStarcom',
        'UG' => 'Ugoos',
        'U1' => 'Uhans',
        'UH' => 'Uhappy',
        'UL' => 'Ulefone',
        'UA' => 'Umax',
        'UZ' => 'Unihertz',
        'UX' => 'Unimax',
        'US' => 'Uniscope',
        'UO' => 'Unnecto',
        'UU' => 'Unonu',
        'UN' => 'Unowhy',
        '5V' => 'VAIO',
        'V6' => 'VGO TEL',
        'VK' => 'VK Mobile',
        'V0' => 'VKworld',
        '3V' => 'VVETIME',
        'VA' => 'Vastking',
        'VN' => 'Venso',
        '4V' => 'Verico',
        'V4' => 'Verizon',
        'VR' => 'Vernee',
        'VX' => 'Vertex',
        'VE' => 'Vertu',
        'VL' => 'Verykool',
        'V8' => 'Vesta',
        'VT' => 'Vestel',
        'VD' => 'Videocon',
        'VW' => 'Videoweb',
        'VS' => 'ViewSonic',
        'V7' => 'Vinga',
        'V3' => 'Vinsoc',
        '0V' => 'Vipro',
        'VI' => 'Vitelcom',
        '8V' => 'Viumee',
        'V5' => 'Vivax',
        'VV' => 'Vivo',
        'VZ' => 'Vizio',
        'VM' => 'Vodacom',
        'VF' => 'Vodafone',
        'V2' => 'Vonino',
        '1V' => 'Vontar',
        'VG' => 'Vorago',
        '2V' => 'Vorke',
        'V1' => 'Voto',
        'VO' => 'Voxtel',
        'VY' => 'Voyo',
        'VH' => 'Vsmart',
        'V9' => 'Vsun',
        'VU' => 'Vulcan',
        'WA' => 'Walton',
        'WM' => 'Weimei',
        'WE' => 'WellcoM',
        'WY' => 'Wexler',
        'WP' => 'Wieppo',
        'W2' => 'Wigor',
        'WI' => 'Wiko',
        'WF' => 'Wileyfox',
        'WS' => 'Winds',
        'WN' => 'Wink',
        '9W' => 'Winmax',
        'W0' => 'Wiseasy',
        '2W' => 'Wizz',
        'WL' => 'Wolder',
        'WG' => 'Wolfgang',
        'WO' => 'Wonu',
        'W1' => 'Woo',
        'WR' => 'Wortmann',
        'WX' => 'Woxter',
        'X3' => 'X-BO',
        'XT' => 'X-TIGI',
        'XV' => 'X-View',
        'XG' => 'Xgody',
        'XL' => 'Xiaolajiao',
        'XI' => 'Xiaomi',
        'XN' => 'Xion',
        'XO' => 'Xolo',
        'XR' => 'Xoro',
        'XS' => 'Xshitou',
        '4X' => 'Xtouch',
        'YD' => 'Yandex',
        'YA' => 'Yarvik',
        'Y2' => 'Yes',
        'YE' => 'Yezz',
        'YK' => 'Yoka TV',
        'YO' => 'Yota',
        'YT' => 'Ytone',
        'Y1' => 'Yu',
        'YU' => 'Yuandao',
        'YS' => 'Yusun',
        'YX' => 'Yxtel',
        'ZT' => 'ZTE',
        'ZQ' => 'ZYQ',
        '0Z' => 'Zatec',
        'PZ' => 'Zebra',
        'ZE' => 'Zeemi',
        'ZN' => 'Zen',
        'ZK' => 'Zenek',
        'ZL' => 'Zentality',
        'ZF' => 'Zfiner',
        'ZI' => 'Zidoo',
        'ZX' => 'Ziox',
        'ZO' => 'Zonda',
        'ZP' => 'Zopo',
        'ZU' => 'Zuum',
        'ZY' => 'Zync',
        'A5' => 'altron',
        'B4' => 'bogo',
        'BX' => 'bq',
        '6E' => 'eSTAR',
        'ET' => 'eTouch',
        '3I' => 'i-Cherry',
        'IJ' => 'i-Joy',
        'IM' => 'i-mate',
        'IO' => 'i-mobile',
        'IB' => 'iBall',
        'IY' => 'iBerry',
        '7I' => 'iBrit',
        'IC' => 'iDroid',
        'IG' => 'iGet',
        'IH' => 'iHunt',
        'IK' => 'iKoMo',
        'I7' => 'iLA',
        '2I' => 'iLife',
        '1I' => 'iMars',
        'IW' => 'iNew',
        'I1' => 'iOcean',
        'IP' => 'iPro',
        'IR' => 'iRola',
        'IU' => 'iRulu',
        '9I' => 'iSWAG',
        'IZ' => 'iTel',
        '0I' => 'iTruck',
        'I8' => 'iVA',
        'IE' => 'iView',
        '0J' => 'iVooMi',
        'I9' => 'iZotron',
        '09' => 'meanIT',
        'PO' => 'phoneOne',
        'TZ' => 'teXet',
        'OW' => 'öwn',

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
        return (string) \array_search($brand, self::$deviceBrands) ?: '';
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
