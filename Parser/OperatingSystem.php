<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser;

/**
 * Class OperatingSystem
 *
 * Parses the useragent for operating system information
 *
 * Detected operating systems can be found in self::$operatingSystems and /regexes/oss.yml
 * This class also defined some operating system families and methods to get the family for a specific os
 */
class OperatingSystem extends AbstractParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/oss.yml';

    /**
     * @var string
     */
    protected $parserName = 'os';

    /**
     * Known operating systems mapped to their internal short codes
     *
     * @var array
     */
    protected static $operatingSystems = [
        'AIX' => 'AIX',
        'AND' => 'Android',
        'ADR' => 'Android TV',
        'AMG' => 'AmigaOS',
        'ATV' => 'tvOS',
        'ARL' => 'Arch Linux',
        'BTR' => 'BackTrack',
        'SBA' => 'Bada',
        'BEO' => 'BeOS',
        'BLB' => 'BlackBerry OS',
        'QNX' => 'BlackBerry Tablet OS',
        'BMP' => 'Brew',
        'CAI' => 'Caixa Mágica',
        'CES' => 'CentOS',
        'CLR' => 'ClearOS Mobile',
        'COS' => 'Chrome OS',
        'CHN' => 'China OS',
        'CYN' => 'CyanogenMod',
        'DEB' => 'Debian',
        'DEE' => 'Deepin',
        'DFB' => 'DragonFly',
        'DVK' => 'DVKBuntu',
        'FED' => 'Fedora',
        'FEN' => 'Fenix',
        'FOS' => 'Firefox OS',
        'FIR' => 'Fire OS',
        'FOR' => 'Foresight Linux',
        'FRE' => 'Freebox',
        'BSD' => 'FreeBSD',
        'FYD' => 'FydeOS',
        'FUC' => 'Fuchsia',
        'GNT' => 'Gentoo',
        'GRI' => 'GridOS',
        'GTV' => 'Google TV',
        'HPX' => 'HP-UX',
        'HAI' => 'Haiku OS',
        'IPA' => 'iPadOS',
        'HAR' => 'HarmonyOS',
        'HAS' => 'HasCodingOS',
        'IRI' => 'IRIX',
        'INF' => 'Inferno',
        'JME' => 'Java ME',
        'KOS' => 'KaiOS',
        'KAN' => 'Kanotix',
        'KNO' => 'Knoppix',
        'KTV' => 'KreaTV',
        'KBT' => 'Kubuntu',
        'LIN' => 'GNU/Linux',
        'LND' => 'LindowsOS',
        'LNS' => 'Linspire',
        'LBT' => 'Lubuntu',
        'LOS' => 'Lumin OS',
        'VLN' => 'VectorLinux',
        'MAC' => 'Mac',
        'MAE' => 'Maemo',
        'MAG' => 'Mageia',
        'MDR' => 'Mandriva',
        'SMG' => 'MeeGo',
        'MCD' => 'MocorDroid',
        'MON' => 'moonOS',
        'MIN' => 'Mint',
        'MLD' => 'MildWild',
        'MOR' => 'MorphOS',
        'NBS' => 'NetBSD',
        'MTK' => 'MTK / Nucleus',
        'MRE' => 'MRE',
        'WII' => 'Nintendo',
        'NDS' => 'Nintendo Mobile',
        'OS2' => 'OS/2',
        'T64' => 'OSF1',
        'OBS' => 'OpenBSD',
        'OWR' => 'OpenWrt',
        'OTV' => 'Opera TV',
        'ORD' => 'Ordissimo',
        'PAR' => 'Pardus',
        'PCL' => 'PCLinuxOS',
        'PLA' => 'Plasma Mobile',
        'PSP' => 'PlayStation Portable',
        'PS3' => 'PlayStation',
        'PUR' => 'PureOS',
        'RHT' => 'Red Hat',
        'ROS' => 'RISC OS',
        'ROK' => 'Roku OS',
        'RSO' => 'Rosa',
        'REM' => 'Remix OS',
        'REX' => 'REX',
        'RZD' => 'RazoDroiD',
        'SAB' => 'Sabayon',
        'SSE' => 'SUSE',
        'SAF' => 'Sailfish OS',
        'SEE' => 'SeewoOS',
        'SLW' => 'Slackware',
        'SOS' => 'Solaris',
        'SYL' => 'Syllable',
        'SYM' => 'Symbian',
        'SYS' => 'Symbian OS',
        'S40' => 'Symbian OS Series 40',
        'S60' => 'Symbian OS Series 60',
        'SY3' => 'Symbian^3',
        'TDX' => 'ThreadX',
        'TIZ' => 'Tizen',
        'TOS' => 'TmaxOS',
        'UBT' => 'Ubuntu',
        'WAS' => 'watchOS',
        'WTV' => 'WebTV',
        'WHS' => 'Whale OS',
        'WIN' => 'Windows',
        'WCE' => 'Windows CE',
        'WIO' => 'Windows IoT',
        'WMO' => 'Windows Mobile',
        'WPH' => 'Windows Phone',
        'WRT' => 'Windows RT',
        'XBX' => 'Xbox',
        'XBT' => 'Xubuntu',
        'YNS' => 'YunOS',
        'ZEN' => 'Zenwalk',
        'IOS' => 'iOS',
        'POS' => 'palmOS',
        'WOS' => 'webOS',
    ];

    /**
     * Operating system families mapped to the short codes of the associated operating systems
     *
     * @var array
     */
    protected static $osFamilies = [
        'Android'               => [
            'AND', 'CYN', 'FIR', 'REM', 'RZD', 'MLD', 'MCD', 'YNS', 'GRI', 'HAR',
            'ADR', 'CLR',
        ],
        'AmigaOS'               => ['AMG', 'MOR'],
        'BlackBerry'            => ['BLB', 'QNX'],
        'Brew'                  => ['BMP'],
        'BeOS'                  => ['BEO', 'HAI'],
        'Chrome OS'             => ['COS', 'FYD', 'SEE'],
        'Firefox OS'            => ['FOS', 'KOS'],
        'Gaming Console'        => ['WII', 'PS3'],
        'Google TV'             => ['GTV'],
        'IBM'                   => ['OS2'],
        'iOS'                   => ['IOS', 'ATV', 'WAS', 'IPA'],
        'RISC OS'               => ['ROS'],
        'GNU/Linux'             => [
            'LIN', 'ARL', 'DEB', 'KNO', 'MIN', 'UBT', 'KBT', 'XBT', 'LBT', 'FED',
            'RHT', 'VLN', 'MDR', 'GNT', 'SAB', 'SLW', 'SSE', 'CES', 'BTR', 'SAF',
            'ORD', 'TOS', 'RSO', 'DEE', 'FRE', 'MAG', 'FEN', 'CAI', 'PCL', 'HAS',
            'LOS', 'DVK', 'ROK', 'OWR', 'OTV', 'KTV', 'PUR', 'PLA', 'FUC', 'PAR',
            'FOR', 'MON', 'KAN', 'ZEN', 'LND', 'LNS', 'CHN',
        ],
        'Mac'                   => ['MAC'],
        'Mobile Gaming Console' => ['PSP', 'NDS', 'XBX'],
        'Real-time OS'          => ['MTK', 'TDX', 'MRE', 'JME', 'REX'],
        'Other Mobile'          => ['WOS', 'POS', 'SBA', 'TIZ', 'SMG', 'MAE'],
        'Symbian'               => ['SYM', 'SYS', 'SY3', 'S60', 'S40'],
        'Unix'                  => ['SOS', 'AIX', 'HPX', 'BSD', 'NBS', 'OBS', 'DFB', 'SYL', 'IRI', 'T64', 'INF'],
        'WebTV'                 => ['WTV'],
        'Windows'               => ['WIN'],
        'Windows Mobile'        => ['WPH', 'WMO', 'WCE', 'WRT', 'WIO'],
        'Other Smart TV'        => ['WHS'],
    ];

    /**
     * Operating system families that are known as desktop only
     *
     * @var array
     */
    protected static $desktopOsArray = ['AmigaOS', 'IBM', 'GNU/Linux', 'Mac', 'Unix', 'Windows', 'BeOS', 'Chrome OS'];

    /**
     * Returns all available operating systems
     *
     * @return array
     */
    public static function getAvailableOperatingSystems(): array
    {
        return self::$operatingSystems;
    }

    /**
     * Returns all available operating system families
     *
     * @return array
     */
    public static function getAvailableOperatingSystemFamilies(): array
    {
        return self::$osFamilies;
    }

    /**
     * Returns the os name and shot name
     *
     * @param string $name
     *
     * @return array
     */
    public static function getShortOsData(string $name): array
    {
        $short = 'UNK';

        foreach (self::$operatingSystems as $osShort => $osName) {
            if (\strtolower($name) !== \strtolower($osName)) {
                continue;
            }

            $name  = $osName;
            $short = $osShort;

            break;
        }

        return \compact('short', 'name');
    }

    /**
     * @inheritdoc
     */
    public function parse(): ?array
    {
        $return = $osRegex = $matches = [];

        foreach ($this->getRegexes() as $osRegex) {
            $matches = $this->matchUserAgent($osRegex['regex']);

            if ($matches) {
                break;
            }
        }

        if (empty($matches)) {
            return $return;
        }

        $name                                = $this->buildByMatch($osRegex['name'], $matches);
        ['name' => $name, 'short' => $short] = self::getShortOsData($name);

        $version = \array_key_exists('version', $osRegex)
            ? $this->buildVersion((string) $osRegex['version'], $matches)
            : '';

        foreach ($osRegex['versions'] ?? [] as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);

            if (!$matches) {
                continue;
            }

            if (\array_key_exists('name', $regex)) {
                $name                                = $this->buildByMatch($regex['name'], $matches);
                ['name' => $name, 'short' => $short] = self::getShortOsData($name);
            }

            if (\array_key_exists('version', $regex)) {
                $version = $this->buildVersion((string) $regex['version'], $matches);
            }

            break;
        }

        $return = [
            'name'       => $name,
            'short_name' => $short,
            'version'    => $version,
            'platform'   => $this->parsePlatform(),
            'family'     => self::getOsFamily($short),
        ];

        if (\in_array($return['name'], self::$operatingSystems)) {
            $return['short_name'] = \array_search($return['name'], self::$operatingSystems);
        }

        return $return;
    }

    /**
     * Returns the operating system family for the given operating system
     *
     * @param string $osLabel name or short name
     *
     * @return string|null If null, "Unknown"
     */
    public static function getOsFamily(string $osLabel): ?string
    {
        if (\in_array($osLabel, self::$operatingSystems)) {
            $osLabel = \array_search($osLabel, self::$operatingSystems);
        }

        foreach (self::$osFamilies as $family => $labels) {
            if (\in_array($osLabel, $labels)) {
                return (string) $family;
            }
        }

        return null;
    }

    /**
     * Returns true if OS is desktop
     *
     * @param string $osName OS short name
     *
     * @return bool
     */
    public static function isDesktopOs(string $osName): bool
    {
        $osFamily = self::getOsFamily($osName);

        return \in_array($osFamily, self::$desktopOsArray);
    }

    /**
     * Returns the full name for the given short name
     *
     * @param string      $os
     * @param string|null $ver
     *
     * @return ?string
     */
    public static function getNameFromId(string $os, ?string $ver = null): ?string
    {
        if (\array_key_exists($os, self::$operatingSystems)) {
            $osFullName = self::$operatingSystems[$os];

            return \trim($osFullName . ' ' . $ver);
        }

        return null;
    }

    /**
     * Parse current UserAgent string for the operating system platform
     *
     * @return string
     */
    protected function parsePlatform(): string
    {
        if ($this->matchUserAgent('arm|aarch64|Apple ?TV|Watch ?OS|Watch1,[12]')) {
            return 'ARM';
        }

        if ($this->matchUserAgent('mips')) {
            return 'MIPS';
        }

        if ($this->matchUserAgent('sh4')) {
            return 'SuperH';
        }

        if ($this->matchUserAgent('64-?bit|WOW64|(?:Intel)?x64|win64|amd64|x86_?64')) {
            return 'x64';
        }

        if ($this->matchUserAgent('.+32bit|.+win32|(?:i[0-9]|x)86|i86pc')) {
            return 'x86';
        }

        return '';
    }
}
