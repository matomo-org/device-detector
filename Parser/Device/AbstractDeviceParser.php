<?php

declare(strict_types=1);

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
        '2F' => '2F Mobile',
        '2Q' => '3GNET',
        '3Q' => '3Q',
        '4G' => '4Good',
        '04' => '4ife',
        '36' => '360',
        '88' => '8848',
        '41' => 'A1',
        '00' => 'Accent',
        'AE' => 'Ace',
        'AC' => 'Acer',
        '3K' => 'Acteck',
        'A9' => 'Advan',
        'AD' => 'Advance',
        'AF' => 'AfriOne',
        'A3' => 'AGM',
        'J0' => 'AG Mobile',
        'AZ' => 'Ainol',
        'AI' => 'Airness',
        'AT' => 'Airties',
        '0A' => 'AIS',
        '85' => 'Aiuto',
        'AW' => 'Aiwa',
        'AK' => 'Akai',
        'Q3' => 'AKIRA',
        '1A' => 'Alba',
        'AL' => 'Alcatel',
        '20' => 'Alcor',
        '7L' => 'ALDI NORD',
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
        '54' => 'AMCV',
        'AG' => 'AMGOO',
        '9A' => 'Amigoo',
        'AO' => 'Amoi',
        '60' => 'Andowl',
        '7A' => 'Anry',
        'A0' => 'ANS',
        'O8' => 'AOC',
        '55' => 'AOpen',
        '3N' => 'Aoson',
        'J2' => 'AOYODKG',
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
        'AQ' => 'Aspera',
        'A8' => 'Assistant',
        '1U' => 'Astro',
        'AU' => 'Asus',
        '6A' => 'AT&T',
        '2A' => 'Atom',
        'Z2' => 'Atvio',
        'AX' => 'Audiovox',
        'AJ' => 'AURIS',
        'ZA' => 'Avenzo',
        'AH' => 'AVH',
        'AV' => 'Avvio',
        'XA' => 'Axioo',
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
        '9B' => 'Bellphone',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        '63' => 'Beyond',
        'BZ' => 'Bezkam',
        'BG' => 'BGH',
        '6B' => 'Bigben',
        'B8' => 'BIHEE',
        'BA' => 'BilimLand',
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
        'UB' => 'Bleck',
        '7B' => 'Blloc',
        'Q2' => 'Blow',
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
        'BJ' => 'BrightSign',
        'BR' => 'Brondi',
        'BY' => 'BS Mobile',
        'B1' => 'Bush',
        'C9' => 'CAGI',
        'BC' => 'Camfone',
        'CT' => 'Capitel',
        'CP' => 'Captiva',
        'CF' => 'Carrefour',
        'CS' => 'Casio',
        'R4' => 'Casper',
        'CA' => 'Cat',
        'CJ' => 'Cavion',
        '7C' => 'Celcus',
        'CE' => 'Celkon',
        '02' => 'Cell-C',
        '34' => 'CellAllure',
        '62' => 'Centric',
        'G3' => 'CG Mobile',
        'C2' => 'Changhong',
        'CH' => 'Cherry Mobile',
        'HG' => 'CHIA',
        'CI' => 'Chico Mobile',
        'C3' => 'China Mobile',
        '1C' => 'Chuwi',
        '25' => 'Claresta',
        'L8' => 'Clarmin',
        '33' => 'Clementoni',
        'CD' => 'Cloudfone',
        '6C' => 'Cloudpad',
        'C0' => 'Clout',
        'CN' => 'CnM',
        'XC' => 'Cobalt',
        'CY' => 'Coby Kyros',
        '9C' => 'Colors',
        'C6' => 'Comio',
        'CL' => 'Compal',
        'CQ' => 'Compaq',
        'C7' => 'ComTrade Tesla',
        'C8' => 'Concord',
        'CC' => 'ConCorde',
        'C5' => 'Condor',
        '53' => 'Connectce',
        '8C' => 'Connex',
        '4C' => 'Conquest',
        '3C' => 'Contixo',
        'CO' => 'Coolpad',
        '4R' => 'CORN',
        '1O' => 'Cosmote',
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
        '6D' => 'Datalogic',
        '7D' => 'Datamini',
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
        '9D' => 'Ditecma',
        '1D' => 'Diva',
        'D6' => 'Divisat',
        'X6' => 'DIXON',
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
        'DJ' => 'Dragon Touch',
        'D8' => 'Droxio',
        'DU' => 'Dune HD',
        'EB' => 'E-Boda',
        '2E' => 'E-Ceros',
        'E8' => 'E-tel',
        'EQ' => 'Eagle',
        'EP' => 'Easypix',
        'EA' => 'EBEST',
        'E4' => 'Echo Mobiles',
        '35' => 'ECON',
        'ES' => 'ECS',
        'E6' => 'EE',
        'EY' => 'Einstein',
        'EK' => 'EKO',
        'EM' => 'Eks Mobility',
        '4K' => 'EKT',
        '7E' => 'ELARI',
        '03' => 'Electroneum',
        'Z8' => 'ELECTRONIA',
        'JE' => 'Elekta',
        'L0' => 'Element',
        'EG' => 'Elenberg',
        'EL' => 'Elephone',
        '4E' => 'Eltex',
        'ED' => 'Energizer',
        'E1' => 'Energy Sistem',
        'EJ' => 'Engel',
        '3E' => 'Enot',
        '8E' => 'Epik One',
        'E7' => 'Ergo',
        'EC' => 'Ericsson',
        'ER' => 'Ericy',
        '05' => 'Erisson',
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
        'QE' => 'EWIS',
        'EF' => 'EXCEED',
        'XE' => 'ExMobile',
        'EH' => 'EXO',
        'EX' => 'Explay',
        'E5' => 'Extrem',
        'EI' => 'Ezio',
        'EZ' => 'Ezze',
        'F4' => 'F&U',
        '5F' => 'F150',
        'F6' => 'Facebook',
        'FA' => 'Fairphone',
        'FM' => 'Famoco',
        'FB' => 'Fantec',
        '9R' => 'FaRao Pro',
        '17' => 'FarEasTone',
        'FE' => 'Fengxiang',
        'F7' => 'Fero',
        'FI' => 'FiGO',
        'F1' => 'FinePower',
        'FX' => 'Finlux',
        'F3' => 'FireFly Mobile',
        'F8' => 'FISE',
        'FL' => 'Fly',
        '1F' => 'FMT',
        'FN' => 'FNB',
        'FW' => 'FNF',
        'FD' => 'Fondi',
        'F0' => 'Fonos',
        'F2' => 'FORME',
        'F5' => 'Formuler',
        'FR' => 'Forstar',
        'RF' => 'Fortis',
        '0F' => 'Fourel',
        '44' => 'Four Mobile',
        'FO' => 'Foxconn',
        'FT' => 'Freetel',
        'FG' => 'Fuego',
        'FU' => 'Fujitsu',
        'GT' => 'G-TiDE',
        'G9' => 'G-Touch',
        'GM' => 'Garmin-Asus',
        'GA' => 'Gateway',
        'GD' => 'Gemini',
        'GN' => 'General Mobile',
        '2G' => 'Genesis',
        'G2' => 'GEOFOX',
        'GE' => 'Geotel',
        '0G' => 'GFive',
        'GH' => 'Ghia',
        '2C' => 'Ghong',
        'GG' => 'Gigabyte',
        'GS' => 'Gigaset',
        '1G' => 'Gini',
        'GZ' => 'Ginzzu',
        'GI' => 'Gionee',
        'G4' => 'Globex',
        'GX' => 'GLX',
        'GC' => 'GOCLEVER',
        'G7' => 'GoGEN',
        'GB' => 'Gol Mobile',
        'GL' => 'Goly',
        'G5' => 'Gome',
        'G1' => 'GoMobile',
        'GO' => 'Google',
        'G0' => 'Goophone',
        '6G' => 'Gooweel',
        'GR' => 'Gradiente',
        'GP' => 'Grape',
        'G6' => 'Gree',
        '3G' => 'Greentel',
        '82' => 'Gresso',
        'GU' => 'Grundig',
        'HF' => 'Hafury',
        'HA' => 'Haier',
        'HE' => 'HannSpree',
        'HK' => 'Hardkernel',
        'HS' => 'Hasee',
        'H6' => 'Helio',
        'ZH' => 'Hezire',
        '3H' => 'Hi',
        'HL' => 'Hi-Level',
        'Q1' => 'High Q',
        'H2' => 'Highscreen',
        '1H' => 'Hipstreet',
        'HI' => 'Hisense',
        'HC' => 'Hitachi',
        'H8' => 'Hitech',
        'H1' => 'Hoffmann',
        'H0' => 'Hometech',
        'HM' => 'Homtom',
        'WH' => 'Honeywell',
        'HZ' => 'Hoozo',
        'H7' => 'Horizon',
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
        'H5' => 'Huskee',
        'HY' => 'Hyrican',
        'HN' => 'Hyundai',
        '7H' => 'Hyve',
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
        '2K' => 'IKI Mobile',
        'IK' => 'iKoMo',
        '8I' => 'IKU Mobile',
        'I7' => 'iLA',
        '2I' => 'iLife',
        'U4' => 'iMan',
        '1I' => 'iMars',
        'IL' => 'IMO Mobile',
        'I3' => 'Impression',
        'FC' => 'INCAR',
        '2H' => 'Inch',
        '6I' => 'Inco',
        'IW' => 'iNew',
        'IF' => 'Infinix',
        'I0' => 'InFocus',
        '81' => 'InfoKit',
        'II' => 'Inkti',
        'I5' => 'InnJoo',
        '26' => 'Innos',
        'IN' => 'Innostream',
        'I4' => 'Inoi',
        'IQ' => 'INQ',
        'IS' => 'Insignia',
        'IT' => 'Intek',
        'IX' => 'Intex',
        '32' => 'Invens',
        'IV' => 'Inverto',
        '4I' => 'Invin',
        'I1' => 'iOcean',
        'IP' => 'iPro',
        'QN' => 'iQ&T',
        '8Q' => 'IQM',
        'I6' => 'Irbis',
        '5I' => 'Iris',
        'IR' => 'iRola',
        'IU' => 'iRulu',
        '9I' => 'iSWAG',
        '86' => 'IT',
        'IZ' => 'iTel',
        '0I' => 'iTruck',
        '3U' => 'IUNI',
        'I8' => 'iVA',
        'IE' => 'iView',
        '0J' => 'iVooMi',
        'UI' => 'ivvi',
        'I9' => 'iZotron',
        'JA' => 'JAY-Tech',
        'J6' => 'Jeka',
        'JS' => 'Jesy',
        'JF' => 'JFone',
        'KJ' => 'Jiake',
        'JI' => 'Jiayu',
        'JG' => 'Jinga',
        'VJ' => 'Jivi',
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
        'K0' => 'Kata',
        'K5' => 'KATV1',
        'KZ' => 'Kazam',
        'KD' => 'KDDI',
        'KS' => 'Kempler & Strauss',
        'K3' => 'Keneksi',
        'KX' => 'Kenxinda',
        'K1' => 'Kiano',
        'KI' => 'Kingsun',
        'KF' => 'KINGZONE',
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
        '5K' => 'KREZ',
        'K2' => 'KRONO',
        'KE' => 'Krüger&Matz',
        'KH' => 'KT-Tech',
        'Z6' => 'KUBO',
        'K8' => 'Kuliao',
        '8K' => 'Kult',
        'KU' => 'Kumai',
        '6K' => 'Kurio',
        '64' => 'Kvant',
        'KY' => 'Kyocera',
        'KQ' => 'Kyowon',
        '1K' => 'Kzen',
        'LJ' => 'L-Max',
        'LQ' => 'LAIQ',
        'L6' => 'Land Rover',
        'L2' => 'Landvo',
        'LA' => 'Lanix',
        'LK' => 'Lark',
        'Z3' => 'Laurus',
        'LV' => 'Lava',
        'LC' => 'LCT',
        'LL' => 'Leader Phone',
        'L5' => 'Leagoo',
        'U3' => 'Leben',
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
        'LF' => 'Lifemaxx',
        'LI' => 'Lingwin',
        'LW' => 'Linnex',
        '5L' => 'Linsar',
        'LO' => 'Loewe',
        '1L' => 'Logic',
        'LM' => 'Logicom',
        'YL' => 'Loview',
        'QL' => 'LT Mobile',
        '0L' => 'Lumigon',
        'LU' => 'Lumus',
        'L9' => 'Luna',
        'LR' => 'Luxor',
        'LY' => 'LYF',
        '72' => 'M-Tech',
        'MQ' => 'M.T.T.',
        'MN' => 'M4tel',
        '92' => 'MAC AUDIO',
        'XM' => 'Macoox',
        '23' => 'Magnus',
        'MJ' => 'Majestic',
        '87' => 'Malata',
        'NH' => 'Manhattan',
        '5M' => 'Mann',
        'MA' => 'Manta Multimedia',
        'Z0' => 'Mantra',
        '2M' => 'Masstel',
        '50' => 'Matrix',
        '7M' => 'Maxcom',
        'ZM' => 'Maximus',
        '6X' => 'Maxtron',
        '0D' => 'MAXVI',
        'MW' => 'Maxwest',
        'M0' => 'Maze',
        'YM' => 'Maze Speed',
        '3D' => 'MDC Store',
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
        '2D' => 'MIVO',
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
        'X0' => 'mPhone',
        'MM' => 'Mpman',
        'MZ' => 'MSI',
        '3R' => 'MStar',
        'M9' => 'MTC',
        'N4' => 'MTN',
        '1R' => 'Multilaser',
        '1M' => 'MYFON',
        'MY' => 'MyPhone',
        'M8' => 'Myria',
        '51' => 'Myros',
        '6M' => 'Mystery',
        '3T' => 'MyTab',
        'MG' => 'MyWigo',
        '08' => 'Nabi',
        'NP' => 'Naomi Phone',
        'N7' => 'National',
        'NC' => 'Navcity',
        '6N' => 'Navitech',
        '7V' => 'Navitel',
        'N3' => 'Navon',
        'NE' => 'NEC',
        '8N' => 'Necnot',
        'NF' => 'Neffos',
        '1N' => 'Neomi',
        'NA' => 'Netgear',
        'NU' => 'NeuImage',
        'HB' => 'New Balance',
        'NW' => 'Newgen',
        'N9' => 'Newland',
        '0N' => 'Newman',
        'ND' => 'Newsday',
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
        'JN' => 'NOBUX',
        'NK' => 'Nokia',
        'NM' => 'Nomi',
        '2N' => 'Nomu',
        'NR' => 'Nordmende',
        '7N' => 'NorthTech',
        '5N' => 'Nos',
        'NO' => 'Nous',
        'NQ' => 'Novex',
        'NJ' => 'NuAns',
        'NL' => 'NUU Mobile',
        'N0' => 'Nuvo',
        'NV' => 'Nvidia',
        'NY' => 'NYX Mobile',
        'O3' => 'O+',
        'OT' => 'O2',
        'O7' => 'Oale',
        'OC' => 'OASYS',
        'OB' => 'Obi',
        'O1' => 'Odys',
        '0O' => 'OINOM',
        'O9' => 'Ok',
        'OA' => 'Okapia',
        'QK' => 'OKWU',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OX' => 'Onix',
        'O4' => 'ONN',
        '3O' => 'ONYX BOOX',
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
        'PQ' => 'Pendoo',
        'PG' => 'Pentagram',
        '1P' => 'Phicomm',
        '4P' => 'Philco',
        'PH' => 'Philips',
        '5P' => 'Phonemax',
        'PO' => 'phoneOne',
        'QP' => 'Pico',
        'PI' => 'Pioneer',
        'PJ' => 'PiPO',
        '9O' => 'Pixela',
        '8P' => 'Pixelphone',
        'PX' => 'Pixus',
        '9P' => 'Planet Computers',
        'PY' => 'Ployer',
        'P4' => 'Plum',
        '22' => 'Pluzz',
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
        'PF' => 'PROFiLO',
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
        '0R' => 'Raspberry',
        'R9' => 'Ravoz',
        'RZ' => 'Razer',
        'RC' => 'RCA Tablets',
        '2R' => 'Reach',
        'RB' => 'Readboy',
        'RE' => 'Realme',
        'R8' => 'RED',
        'RD' => 'Reeder',
        'Z9' => 'REGAL',
        'RP' => 'Revo',
        'RI' => 'Rikomagic',
        'RM' => 'RIM',
        'RN' => 'Rinno',
        'RX' => 'Ritmix',
        'R7' => 'Ritzviva',
        'RV' => 'Riviera',
        '6R' => 'Rivo',
        'RR' => 'Roadrover',
        'R1' => 'Rokit',
        'RK' => 'Roku',
        'R3' => 'Rombica',
        'R5' => 'Ross&Moor',
        'RO' => 'Rover',
        'R6' => 'RoverPad',
        'RQ' => 'RoyQueen',
        'RT' => 'RT Project',
        'RG' => 'RugGear',
        'RL' => 'Ruio',
        'RU' => 'Runbo',
        'RY' => 'Ryte',
        '8L' => 'S-TELL',
        'X5' => 'Saba',
        'X1' => 'Safaricom',
        'SG' => 'Sagem',
        '4L' => 'Salora',
        'SA' => 'Samsung',
        'S0' => 'Sanei',
        '12' => 'Sansui',
        'SQ' => 'Santin',
        'SY' => 'Sanyo',
        'S9' => 'Savio',
        'Y4' => 'SCBC',
        'CZ' => 'Schneider',
        '89' => 'Seatel',
        '24' => 'Seeken',
        'G8' => 'SEG',
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
        '61' => 'Seuic',
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
        '98' => 'Simply',
        '52' => 'Singtech',
        '31' => 'Siragon',
        'GK' => 'SKG',
        'SW' => 'Sky',
        'SK' => 'Skyworth',
        '14' => 'Smadl',
        '19' => 'Smailo',
        'SR' => 'Smart',
        '3B' => 'Smartab',
        '80' => 'SMARTEC',
        'SC' => 'Smartfren',
        'S7' => 'Smartisan',
        '1Q' => 'Smotreshka',
        'SF' => 'Softbank',
        '9L' => 'SOLE',
        'JL' => 'SOLO',
        '16' => 'Solone',
        'OI' => 'Sonim',
        '77' => 'SONOS',
        'SO' => 'Sony',
        'SE' => 'Sony Ericsson',
        'X2' => 'Soundmax',
        '8S' => 'Soyes',
        'PK' => 'Spark',
        'FS' => 'SPC',
        '43' => 'Spectralink',
        '6S' => 'Spectrum',
        'SP' => 'Spice',
        'QS' => 'SQOOL',
        'S4' => 'Star',
        'OL' => 'Starlight',
        '18' => 'Starmobile',
        '2S' => 'Starway',
        '45' => 'Starwind',
        'SB' => 'STF Mobile',
        'GQ' => 'STG Telecom',
        'S8' => 'STK',
        'S2' => 'Stonex',
        'ST' => 'Storex',
        '71' => 'StrawBerry',
        '06' => 'Subor',
        '9S' => 'Sugar',
        'SZ' => 'Sumvision',
        '5Y' => 'Sunny',
        '0H' => 'Sunstech',
        'S3' => 'SunVan',
        '5S' => 'Sunvell',
        'SU' => 'SuperSonic',
        '79' => 'SuperTab',
        'S5' => 'Supra',
        'ZS' => 'Suzuki',
        '0W' => 'Swipe',
        'SS' => 'SWISSMOBILITY',
        '1W' => 'Swisstone',
        'W7' => 'SWTV',
        'SM' => 'Symphony',
        '4S' => 'Syrox',
        'TM' => 'T-Mobile',
        'TK' => 'Takara',
        '73' => 'Tambo',
        '9N' => 'Tanix',
        'T5' => 'TB Touch',
        'TC' => 'TCL',
        'T0' => 'TD Systems',
        'H4' => 'Technicolor',
        'Z5' => 'Technika',
        'TX' => 'TechniSat',
        'TT' => 'TechnoTrend',
        'TP' => 'TechPad',
        '9E' => 'Techwood',
        'T7' => 'Teclast',
        'TB' => 'Tecno Mobile',
        '91' => 'TEENO',
        '29' => 'Teknosa',
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
        'Y3' => 'TOKYO',
        'T1' => 'Tolino',
        '0T' => 'Tone',
        'TY' => 'Tooky',
        'T9' => 'Top House',
        'TO' => 'Toplux',
        '42' => 'Topway',
        '7T' => 'Torex',
        'TS' => 'Toshiba',
        'T8' => 'Touchmate',
        '5R' => 'Transpeed',
        'T6' => 'TrekStor',
        'T3' => 'Trevi',
        'TJ' => 'Trifone',
        'J1' => 'Trio',
        '4T' => 'Tronsmart',
        '11' => 'True',
        'JT' => 'True Slim',
        '5C' => 'TTEC',
        'TU' => 'Tunisie Telecom',
        '1T' => 'Turbo',
        'TR' => 'Turbo-X',
        '5T' => 'TurboKids',
        '5X' => 'TurboPad',
        'TV' => 'TVC',
        'TW' => 'TWM',
        '6T' => 'Twoe',
        'Z1' => 'TWZ',
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
        '6U' => 'UTime',
        'UK' => 'UTOK',
        'UT' => 'UTStarcom',
        '3Z' => 'UZ Mobile',
        'JM' => 'v-mobile',
        '5V' => 'VAIO',
        'VP' => 'Vargo',
        'VA' => 'Vastking',
        'WV' => 'VAVA',
        'VB' => 'VC',
        'VQ' => 'Vega',
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
        '9V' => 'Vision Touch',
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
        'Z7' => 'VOX',
        'VO' => 'Voxtel',
        'VY' => 'Voyo',
        'VH' => 'Vsmart',
        'V9' => 'Vsun',
        'VU' => 'Vulcan',
        '3V' => 'VVETIME',
        'WA' => 'Walton',
        '3W' => 'WE',
        'WM' => 'Weimei',
        'WE' => 'WellcoM',
        'W6' => 'WELLINGTON',
        'WD' => 'Western Digital',
        'WT' => 'Westpoint',
        'WY' => 'Wexler',
        'WP' => 'Wieppo',
        'W2' => 'Wigor',
        'WI' => 'Wiko',
        'WF' => 'Wileyfox',
        'WS' => 'Winds',
        'WN' => 'Wink',
        '9W' => 'Winmax',
        'W5' => 'Winnovo',
        'WU' => 'Wintouch',
        'W0' => 'Wiseasy',
        'W4' => 'WIWA',
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
        'QX' => 'XGIMI',
        'XG' => 'Xgody',
        'XL' => 'Xiaolajiao',
        'XI' => 'Xiaomi',
        'XN' => 'Xion',
        'XO' => 'Xolo',
        'XR' => 'Xoro',
        'XS' => 'Xshitou',
        '4X' => 'Xtouch',
        'X8' => 'Xtratech',
        'YD' => 'Yandex',
        'YA' => 'Yarvik',
        'YJ' => 'YASIN',
        'Y2' => 'Yes',
        'YE' => 'Yezz',
        'YK' => 'Yoka TV',
        'YO' => 'Yota',
        'YT' => 'Ytone',
        'Y1' => 'Yu',
        'YU' => 'Yuandao',
        'Y0' => 'YUHO',
        'YN' => 'Yuno',
        'YS' => 'Yusun',
        'YX' => 'Yxtel',
        '2Z' => 'Zaith',
        '0Z' => 'Zatec',
        'PZ' => 'Zebra',
        'ZE' => 'Zeemi',
        'ZN' => 'Zen',
        'ZK' => 'Zenek',
        'ZL' => 'Zentality',
        'ZF' => 'Zfiner',
        'Z4' => 'ZH&K',
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
