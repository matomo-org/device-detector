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
        '3Q' => '3Q',
        '4G' => '4Good',
        '04' => '4ife',
        '36' => '360',
        '88' => '8848',
        '41' => 'A1',
        '00' => 'Accent',
        'AE' => 'Ace',
        'AC' => 'Acer',
        'A9' => 'Advan',
        'AD' => 'Advance',
        'A3' => 'AGM',
        'AZ' => 'Ainol',
        'AI' => 'Airness',
        'AT' => 'Airties',
        '0A' => 'AIS',
        'AW' => 'Aiwa',
        'AK' => 'Akai',
        '1A' => 'Alba',
        'AL' => 'Alcatel',
        '20' => 'Alcor',
        '6L' => 'ALDI SÜD',
        '3L' => 'Alfawise',
        '4A' => 'Aligator',
        'AA' => 'AllCall',
        '3A' => 'AllDocube',
        'A2' => 'Allview',
        'A7' => 'Allwinner',
        'A1' => 'Altech UEC',
        '66' => 'Altice',
        'A5' => 'altron',
        'KN' => 'Amazon',
        'AG' => 'AMGOO',
        '9A' => 'Amigoo',
        'AO' => 'Amoi',
        '60' => 'Andowl',
        '7A' => 'Anry',
        'A0' => 'ANS',
        '3N' => 'Aoson',
        'AP' => 'Apple',
        'AR' => 'Archos',
        'AB' => 'Arian Space',
        'A6' => 'Ark',
        '5A' => 'ArmPhone',
        'AN' => 'Arnova',
        'AS' => 'ARRIS',
        '40' => 'Artel',
        '21' => 'Artizlee',
        '8A' => 'Asano',
        '90' => 'Asanzo',
        'A4' => 'Ask',
        'A8' => 'Assistant',
        'AU' => 'Asus',
        '6A' => 'AT&T',
        '2A' => 'Atom',
        'AX' => 'Audiovox',
        'ZA' => 'Avenzo',
        'AH' => 'AVH',
        'AV' => 'Avvio',
        'AY' => 'Axxion',
        'AM' => 'Azumi Mobile',
        'BO' => 'BangOlufsen',
        'BN' => 'Barnes & Noble',
        'BB' => 'BBK',
        '0B' => 'BB Mobile',
        'B6' => 'BDF',
        'BE' => 'Becker',
        'B5' => 'Beeline',
        'B0' => 'Beelink',
        'BL' => 'Beetel',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        'BZ' => 'Bezkam',
        'BG' => 'BGH',
        '6B' => 'Bigben',
        'B8' => 'BIHEE',
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
        'B4' => 'bogo',
        'BW' => 'Boway',
        'BX' => 'bq',
        '8B' => 'Brandt',
        'BV' => 'Bravis',
        'BR' => 'Brondi',
        'B1' => 'Bush',
        'C9' => 'CAGI',
        'CT' => 'Capitel',
        'CP' => 'Captiva',
        'CF' => 'Carrefour',
        'CS' => 'Casio',
        'R4' => 'Casper',
        'CA' => 'Cat',
        '7C' => 'Celcus',
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
        'C6' => 'Comio',
        'CL' => 'Compal',
        'CQ' => 'Compaq',
        'C7' => 'ComTrade Tesla',
        'C8' => 'Concord',
        'CC' => 'ConCorde',
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
        'CB' => 'CUBOT',
        'CV' => 'CVTE',
        'C4' => 'Cyrus',
        'D5' => 'Daewoo',
        'DA' => 'Danew',
        'DT' => 'Datang',
        'D7' => 'Datawind',
        'D1' => 'Datsun',
        'DB' => 'Dbtel',
        'DL' => 'Dell',
        'DE' => 'Denver',
        'DS' => 'Desay',
        'DW' => 'DeWalt',
        'DX' => 'DEXP',
        'DG' => 'Dialog',
        'DI' => 'Dicam',
        'D4' => 'Digi',
        'D3' => 'Digicel',
        'DH' => 'Digihome',
        'DD' => 'Digiland',
        'D2' => 'Digma',
        '1D' => 'Diva',
        'D6' => 'Divisat',
        'DM' => 'DMM',
        'DN' => 'DNS',
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
        'EP' => 'Easypix',
        'EA' => 'EBEST',
        'E4' => 'Echo Mobiles',
        'ES' => 'ECS',
        'E6' => 'EE',
        'EK' => 'EKO',
        'EM' => 'Eks Mobility',
        '7E' => 'ELARI',
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
        '6E' => 'eSTAR',
        'EN' => 'Eton',
        'ET' => 'eTouch',
        '1E' => 'Etuline',
        'EU' => 'Eurostar',
        'E9' => 'Evercoss',
        'EV' => 'Evertek',
        'E3' => 'Evolio',
        'EO' => 'Evolveo',
        'E0' => 'EvroMedia',
        'XE' => 'ExMobile',
        'EH' => 'EXO',
        'EX' => 'Explay',
        'E5' => 'Extrem',
        'EI' => 'Ezio',
        'EZ' => 'Ezze',
        'FA' => 'Fairphone',
        'FM' => 'Famoco',
        '17' => 'FarEasTone',
        'FE' => 'Fengxiang',
        'F7' => 'Fero',
        'FI' => 'FiGO',
        'F1' => 'FinePower',
        'FX' => 'Finlux',
        'F3' => 'FireFly Mobile',
        'FL' => 'Fly',
        'FN' => 'FNB',
        'FD' => 'Fondi',
        'F0' => 'Fonos',
        'F2' => 'FORME',
        'FR' => 'Forstar',
        'RF' => 'Fortis',
        'FO' => 'Foxconn',
        'FT' => 'Freetel',
        'FU' => 'Fujitsu',
        'GT' => 'G-TiDE',
        'GM' => 'Garmin-Asus',
        'GA' => 'Gateway',
        'GD' => 'Gemini',
        'GN' => 'General Mobile',
        'G2' => 'GEOFOX',
        'GE' => 'Geotel',
        'GH' => 'Ghia',
        '2C' => 'Ghong',
        'GG' => 'Gigabyte',
        'GS' => 'Gigaset',
        'GZ' => 'Ginzzu',
        'GI' => 'Gionee',
        'G4' => 'Globex',
        'GC' => 'GOCLEVER',
        'GL' => 'Goly',
        'G5' => 'Gome',
        'G1' => 'GoMobile',
        'GO' => 'Google',
        'G0' => 'Goophone',
        'GR' => 'Gradiente',
        'GP' => 'Grape',
        'G6' => 'Gree',
        'GU' => 'Grundig',
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
        'HC' => 'Hitachi',
        'H1' => 'Hoffmann',
        'H0' => 'Hometech',
        'HM' => 'Homtom',
        'HZ' => 'Hoozo',
        'HO' => 'Hosin',
        'H3' => 'Hotel',
        'HV' => 'Hotwav',
        'HW' => 'How',
        'HP' => 'HP',
        'HT' => 'HTC',
        'HD' => 'Huadoo',
        'HU' => 'Huawei',
        'HX' => 'Humax',
        'HR' => 'Hurricane',
        'HY' => 'Hyrican',
        'HN' => 'Hyundai',
        '3I' => 'i-Cherry',
        'IJ' => 'i-Joy',
        'IM' => 'i-mate',
        'IO' => 'i-mobile',
        'IB' => 'iBall',
        'IY' => 'iBerry',
        '7I' => 'iBrit',
        'I2' => 'IconBIT',
        'IC' => 'iDroid',
        'IG' => 'iGet',
        'IH' => 'iHunt',
        'IA' => 'Ikea',
        'IK' => 'iKoMo',
        'I7' => 'iLA',
        '2I' => 'iLife',
        '1I' => 'iMars',
        'IL' => 'IMO Mobile',
        'I3' => 'Impression',
        '6I' => 'Inco',
        'IW' => 'iNew',
        'IF' => 'Infinix',
        'I0' => 'InFocus',
        'II' => 'Inkti',
        'I5' => 'InnJoo',
        'IN' => 'Innostream',
        'I4' => 'Inoi',
        'IQ' => 'INQ',
        'IS' => 'Insignia',
        'IT' => 'Intek',
        'IX' => 'Intex',
        'IV' => 'Inverto',
        '4I' => 'Invin',
        'I1' => 'iOcean',
        'IP' => 'iPro',
        '8Q' => 'IQM',
        'I6' => 'Irbis',
        '5I' => 'Iris',
        'IR' => 'iRola',
        'IU' => 'iRulu',
        '9I' => 'iSWAG',
        'IZ' => 'iTel',
        '0I' => 'iTruck',
        'I8' => 'iVA',
        'IE' => 'iView',
        '0J' => 'iVooMi',
        'I9' => 'iZotron',
        'JA' => 'JAY-Tech',
        'JF' => 'JFone',
        'JI' => 'Jiayu',
        'JG' => 'Jinga',
        'JK' => 'JKL',
        'JO' => 'Jolla',
        'J5' => 'Just5',
        'JV' => 'JVC',
        'KT' => 'K-Touch',
        'K4' => 'Kaan',
        'K7' => 'Kaiomy',
        'KL' => 'Kalley',
        'K6' => 'Kanji',
        'KA' => 'Karbonn',
        'K5' => 'KATV1',
        'KZ' => 'Kazam',
        'KD' => 'KDDI',
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
        'KP' => 'KOPO',
        'KR' => 'Koridy',
        'K2' => 'KRONO',
        'KE' => 'Krüger&Matz',
        'KH' => 'KT-Tech',
        'K8' => 'Kuliao',
        '8K' => 'Kult',
        'KU' => 'Kumai',
        'KY' => 'Kyocera',
        '1K' => 'Kzen',
        'LQ' => 'LAIQ',
        'L6' => 'Land Rover',
        'L2' => 'Landvo',
        'LA' => 'Lanix',
        'LK' => 'Lark',
        'LV' => 'Lava',
        'LC' => 'LCT',
        'L5' => 'Leagoo',
        'LD' => 'Ledstar',
        'L1' => 'LeEco',
        '4B' => 'Leff',
        'L4' => 'Lemhoov',
        'LN' => 'Lenco',
        'LE' => 'Lenovo',
        'LT' => 'Leotec',
        'LP' => 'Le Pan',
        'L7' => 'Lephone',
        'LZ' => 'Lesia',
        'L3' => 'Lexand',
        'LX' => 'Lexibook',
        'LG' => 'LG',
        'LI' => 'Lingwin',
        '5L' => 'Linsar',
        'LO' => 'Loewe',
        '1L' => 'Logic',
        'LM' => 'Logicom',
        '0L' => 'Lumigon',
        'LU' => 'Lumus',
        'L9' => 'Luna',
        'LR' => 'Luxor',
        'LY' => 'LYF',
        'MQ' => 'M.T.T.',
        'MN' => 'M4tel',
        'XM' => 'Macoox',
        'MJ' => 'Majestic',
        '5M' => 'Mann',
        'MA' => 'Manta Multimedia',
        '2M' => 'Masstel',
        '50' => 'Matrix',
        '7M' => 'Maxcom',
        '6X' => 'Maxtron',
        '0D' => 'MAXVI',
        'MW' => 'Maxwest',
        'M0' => 'Maze',
        '09' => 'meanIT',
        'M3' => 'Mecer',
        '0M' => 'Mecool',
        'MC' => 'Mediacom',
        'MK' => 'MediaTek',
        'MD' => 'Medion',
        'M2' => 'MEEG',
        'MP' => 'MegaFon',
        '3M' => 'Meitu',
        'M1' => 'Meizu',
        '0E' => 'Melrose',
        'MU' => 'Memup',
        'ME' => 'Metz',
        'MX' => 'MEU',
        'MI' => 'MicroMax',
        'MS' => 'Microsoft',
        '1X' => 'Minix',
        'OM' => 'Mintt',
        'MO' => 'Mio',
        'M7' => 'Miray',
        '8M' => 'Mito',
        'MT' => 'Mitsubishi',
        'M5' => 'MIXC',
        '1Z' => 'MiXzo',
        'ML' => 'MLLED',
        'LS' => 'MLS',
        '4M' => 'Mobicel',
        'M6' => 'Mobiistar',
        'MH' => 'Mobiola',
        'MB' => 'Mobistel',
        '6W' => 'MobiWire',
        '9M' => 'Mobo',
        'M4' => 'Modecom',
        'MF' => 'Mofut',
        'MR' => 'Motorola',
        'MV' => 'Movic',
        'MM' => 'Mpman',
        'MZ' => 'MSI',
        'M9' => 'MTC',
        'N4' => 'MTN',
        '1R' => 'Multilaser',
        '1M' => 'MYFON',
        'MY' => 'MyPhone',
        'M8' => 'Myria',
        '6M' => 'Mystery',
        '3T' => 'MyTab',
        'MG' => 'MyWigo',
        'N7' => 'National',
        '6N' => 'Navitech',
        'N3' => 'Navon',
        'NE' => 'NEC',
        'NF' => 'Neffos',
        '1N' => 'Neomi',
        'NA' => 'Netgear',
        'NU' => 'NeuImage',
        'NW' => 'Newgen',
        'N9' => 'Newland',
        '0N' => 'Newman',
        'NS' => 'NewsMy',
        'XB' => 'NEXBOX',
        'NX' => 'Nexian',
        'N8' => 'NEXON',
        'N2' => 'Nextbit',
        'NT' => 'NextBook',
        '4N' => 'NextTab',
        'NG' => 'NGM',
        'NZ' => 'NG Optics',
        'NN' => 'Nikon',
        'NI' => 'Nintendo',
        'N5' => 'NOA',
        'N1' => 'Noain',
        'N6' => 'Nobby',
        'NB' => 'Noblex',
        'NK' => 'Nokia',
        'NM' => 'Nomi',
        '2N' => 'Nomu',
        'NR' => 'Nordmende',
        '7N' => 'NorthTech',
        '5N' => 'Nos',
        'NO' => 'Nous',
        'NJ' => 'NuAns',
        'NL' => 'NUU Mobile',
        'N0' => 'Nuvo',
        'NV' => 'Nvidia',
        'NY' => 'NYX Mobile',
        'O3' => 'O+',
        'OT' => 'O2',
        'O7' => 'Oale',
        'OB' => 'Obi',
        'O1' => 'Odys',
        'OA' => 'Okapia',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OX' => 'Onix',
        'O4' => 'ONN',
        '2O' => 'OpelMobile',
        'OH' => 'Openbox',
        'OP' => 'OPPO',
        'OO' => 'Opsson',
        'OR' => 'Orange',
        'O5' => 'Orbic',
        'OS' => 'Ordissimo',
        'OK' => 'Ouki',
        'OE' => 'Oukitel',
        'OU' => 'OUYA',
        'OV' => 'Overmax',
        '30' => 'Ovvi',
        'O2' => 'Owwo',
        'OY' => 'Oysters',
        'O6' => 'Oyyu',
        'OZ' => 'OzoneHD',
        '7P' => 'P-UP',
        'PM' => 'Palm',
        'PN' => 'Panacom',
        'PA' => 'Panasonic',
        'PT' => 'Pantech',
        'PB' => 'PCBOX',
        'PC' => 'PCD',
        'PD' => 'PCD Argentina',
        'PE' => 'PEAQ',
        'PG' => 'Pentagram',
        '1P' => 'Phicomm',
        '4P' => 'Philco',
        'PH' => 'Philips',
        '5P' => 'Phonemax',
        'PO' => 'phoneOne',
        'PI' => 'Pioneer',
        '8P' => 'Pixelphone',
        'PX' => 'Pixus',
        '9P' => 'Planet Computers',
        'PY' => 'Ployer',
        'P4' => 'Plum',
        'P8' => 'PocketBook',
        '0P' => 'POCO',
        'PV' => 'Point of View',
        'PL' => 'Polaroid',
        'PP' => 'PolyPad',
        'P5' => 'Polytron',
        'P2' => 'Pomp',
        'P0' => 'Poppox',
        'PS' => 'Positivo',
        '3P' => 'Positivo BGH',
        'P3' => 'PPTV',
        'FP' => 'Premio',
        'PR' => 'Prestigio',
        'P9' => 'Primepad',
        '6P' => 'Primux',
        '2P' => 'Prixton',
        'P6' => 'Proline',
        'P1' => 'ProScan',
        'P7' => 'Protruly',
        'PU' => 'PULID',
        'QH' => 'Q-Touch',
        'QB' => 'Q.Bell',
        'QI' => 'Qilive',
        'QM' => 'QMobile',
        'QT' => 'Qtek',
        'QA' => 'Quantum',
        'QU' => 'Quechua',
        'QO' => 'Qumo',
        'R2' => 'R-TV',
        'RA' => 'Ramos',
        'R9' => 'Ravoz',
        'RZ' => 'Razer',
        'RC' => 'RCA Tablets',
        '2R' => 'Reach',
        'RB' => 'Readboy',
        'RE' => 'Realme',
        'R8' => 'RED',
        'RD' => 'Reeder',
        'RI' => 'Rikomagic',
        'RM' => 'RIM',
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
        'RT' => 'RT Project',
        'RG' => 'RugGear',
        'RU' => 'Runbo',
        'RY' => 'Ryte',
        'X1' => 'Safaricom',
        'SG' => 'Sagem',
        '4L' => 'Salora',
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
        '0S' => 'SEMP TCL',
        'S1' => 'Sencor',
        'SN' => 'Sendo',
        '01' => 'Senkatel',
        'S6' => 'Senseit',
        'EW' => 'Senwa',
        'SX' => 'SFR',
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
        '19' => 'Smailo',
        'SR' => 'Smart',
        '80' => 'SMARTEC',
        'SC' => 'Smartfren',
        'S7' => 'Smartisan',
        'SF' => 'Softbank',
        '16' => 'Solone',
        'OI' => 'Sonim',
        'SO' => 'Sony',
        'SE' => 'Sony Ericsson',
        'X2' => 'Soundmax',
        '8S' => 'Soyes',
        'FS' => 'SPC',
        '6S' => 'Spectrum',
        'SP' => 'Spice',
        'QS' => 'SQOOL',
        'S4' => 'Star',
        'OL' => 'Starlight',
        '18' => 'Starmobile',
        '2S' => 'Starway',
        'SB' => 'STF Mobile',
        'S8' => 'STK',
        'S2' => 'Stonex',
        'ST' => 'Storex',
        '9S' => 'Sugar',
        'SZ' => 'Sumvision',
        '0H' => 'Sunstech',
        'S3' => 'SunVan',
        '5S' => 'Sunvell',
        'SU' => 'SuperSonic',
        'S5' => 'Supra',
        '0W' => 'Swipe',
        'SS' => 'SWISSMOBILITY',
        '1W' => 'Swisstone',
        'SM' => 'Symphony',
        '4S' => 'Syrox',
        'TM' => 'T-Mobile',
        'TK' => 'Takara',
        '9N' => 'Tanix',
        'T5' => 'TB Touch',
        'TC' => 'TCL',
        'T0' => 'TD Systems',
        'TX' => 'TechniSat',
        'TT' => 'TechnoTrend',
        'TP' => 'TechPad',
        '9E' => 'Techwood',
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
        'TZ' => 'teXet',
        'T4' => 'ThL',
        'TN' => 'Thomson',
        'O0' => 'Thuraya',
        'TI' => 'TIANYU',
        '8T' => 'Time2',
        'TQ' => 'Timovi',
        '2T' => 'Tinai',
        'TF' => 'Tinmo',
        'TH' => 'TiPhone',
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
        '5C' => 'TTEC',
        'TU' => 'Tunisie Telecom',
        '1T' => 'Turbo',
        'TR' => 'Turbo-X',
        '5T' => 'TurboKids',
        'TV' => 'TVC',
        'TW' => 'TWM',
        '6T' => 'Twoe',
        '15' => 'Tymes',
        'UC' => 'U.S. Cellular',
        'UG' => 'Ugoos',
        'U1' => 'Uhans',
        'UH' => 'Uhappy',
        'UL' => 'Ulefone',
        'UA' => 'Umax',
        'UM' => 'UMIDIGI',
        'UZ' => 'Unihertz',
        'UX' => 'Unimax',
        'US' => 'Uniscope',
        'U2' => 'UNIWA',
        'UO' => 'Unnecto',
        'UU' => 'Unonu',
        'UN' => 'Unowhy',
        'UK' => 'UTOK',
        'UT' => 'UTStarcom',
        '5V' => 'VAIO',
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
        'V6' => 'VGO TEL',
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
        'VK' => 'VK Mobile',
        'V0' => 'VKworld',
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
        '3V' => 'VVETIME',
        'WA' => 'Walton',
        'WM' => 'Weimei',
        'WE' => 'WellcoM',
        'WT' => 'Westpoint',
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
        'X4' => 'X.Vision',
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
        'ZT' => 'ZTE',
        'ZU' => 'Zuum',
        'ZY' => 'Zync',
        'ZQ' => 'ZYQ',
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
