<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser;

/**
 * Class OperatingSystem
 *
 * Parses the useragent for operating system information
 *
 * Detected operating systems can be found in self::$operatingSystems and /regexes/oss.yml
 * This class also defined some operating system families and methods to get the family for a specific os
 *
 * @package DeviceDetector\Parser
 */
class OperatingSystem extends ParserAbstract
{
    protected $fixtureFile = 'regexes/oss.yml';
    protected $parserName = 'os';

    /**
     * Known operating systems mapped to their internal short codes
     *
     * @var array
     */
    protected static $operatingSystems = array(
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
        'CYN' => 'CyanogenMod',
        'DEB' => 'Debian',
        'DFB' => 'DragonFly',
        'FED' => 'Fedora',
        'FOS' => 'Firefox OS',
        'FIR' => 'Fire OS',
        'BSD' => 'FreeBSD',
        'GNT' => 'Gentoo',
        'GTV' => 'Google TV',
        'HPX' => 'HP-UX',
        'HAI' => 'Haiku OS',
        'IRI' => 'IRIX',
        'INF' => 'Inferno',
        'KOS' => 'KaiOS',
        'KNO' => 'Knoppix',
        'KBT' => 'Kubuntu',
        'LIN' => 'GNU/Linux',
        'LBT' => 'Lubuntu',
        'VLN' => 'VectorLinux',
        'MAC' => 'Mac',
        'MAE' => 'Maemo',
        'MDR' => 'Mandriva',
        'SMG' => 'MeeGo',
        'MCD' => 'MocorDroid',
        'MIN' => 'Mint',
        'MLD' => 'MildWild',
        'MOR' => 'MorphOS',
        'NBS' => 'NetBSD',
        'MTK' => 'MTK / Nucleus',
        'WII' => 'Nintendo',
        'NDS' => 'Nintendo Mobile',
        'OS2' => 'OS/2',
        'T64' => 'OSF1',
        'OBS' => 'OpenBSD',
        'ORD' => 'Ordissimo',
        'PSP' => 'PlayStation Portable',
        'PS3' => 'PlayStation',
        'RHT' => 'Red Hat',
        'ROS' => 'RISC OS',
        'REM' => 'Remix OS',
        'RZD' => 'RazoDroiD',
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
        'TDX' => 'ThreadX',
        'TIZ' => 'Tizen',
        'UBT' => 'Ubuntu',
        'WTV' => 'WebTV',
        'WIN' => 'Windows',
        'WCE' => 'Windows CE',
        'WIO' => 'Windows IoT',
        'WMO' => 'Windows Mobile',
        'WPH' => 'Windows Phone',
        'WRT' => 'Windows RT',
        'XBX' => 'Xbox',
        'XBT' => 'Xubuntu',
        'YNS' => 'YunOs',
        'IOS' => 'iOS',
        'POS' => 'palmOS',
        'WOS' => 'webOS'
    );

    /**
     * Operating system families mapped to the short codes of the associated operating systems
     *
     * @var array
     */
    protected static $osFamilies = array(
        'Android'               => array('AND', 'CYN', 'FIR', 'REM', 'RZD', 'MLD', 'MCD', 'YNS'),
        'AmigaOS'               => array('AMG', 'MOR'),
        'Apple TV'              => array('ATV'),
        'BlackBerry'            => array('BLB', 'QNX'),
        'Brew'                  => array('BMP'),
        'BeOS'                  => array('BEO', 'HAI'),
        'Chrome OS'             => array('COS'),
        'Firefox OS'            => array('FOS', 'KOS'),
        'Gaming Console'        => array('WII', 'PS3'),
        'Google TV'             => array('GTV'),
        'IBM'                   => array('OS2'),
        'iOS'                   => array('IOS'),
        'RISC OS'               => array('ROS'),
        'GNU/Linux'             => array('LIN', 'ARL', 'DEB', 'KNO', 'MIN', 'UBT', 'KBT', 'XBT', 'LBT', 'FED', 'RHT', 'VLN', 'MDR', 'GNT', 'SAB', 'SLW', 'SSE', 'CES', 'BTR', 'SAF', 'ORD'),
        'Mac'                   => array('MAC'),
        'Mobile Gaming Console' => array('PSP', 'NDS', 'XBX'),
        'Real-time OS'          => array('MTK', 'TDX'),
        'Other Mobile'          => array('WOS', 'POS', 'SBA', 'TIZ', 'SMG', 'MAE'),
        'Symbian'               => array('SYM', 'SYS', 'SY3', 'S60', 'S40'),
        'Unix'                  => array('SOS', 'AIX', 'HPX', 'BSD', 'NBS', 'OBS', 'DFB', 'SYL', 'IRI', 'T64', 'INF'),
        'WebTV'                 => array('WTV'),
        'Windows'               => array('WIN'),
        'Windows Mobile'        => array('WPH', 'WMO', 'WCE', 'WRT', 'WIO')
    );

    /**
     * Returns all available operating systems
     *
     * @return array
     */
    public static function getAvailableOperatingSystems()
    {
        return self::$operatingSystems;
    }

    /**
     * Returns all available operating system families
     *
     * @return array
     */
    public static function getAvailableOperatingSystemFamilies()
    {
        return self::$osFamilies;
    }

    public function parse()
    {
        $return = array();

        foreach ($this->getRegexes() as $osRegex) {
            $matches = $this->matchUserAgent($osRegex['regex']);
            if ($matches) {
                break;
            }
        }

        if (!$matches) {
            return $return;
        }

        $name  = $this->buildByMatch($osRegex['name'], $matches);
        $short = 'UNK';

        foreach (self::$operatingSystems as $osShort => $osName) {
            if (strtolower($name) == strtolower($osName)) {
                $name  = $osName;
                $short = $osShort;
            }
        }

        $return = array(
            'name'       => $name,
            'short_name' => $short,
            'version'    => $this->buildVersion($osRegex['version'], $matches),
            'platform'   => $this->parsePlatform()
        );

        if (in_array($return['name'], self::$operatingSystems)) {
            $return['short_name'] = array_search($return['name'], self::$operatingSystems);
        }

        return $return;
    }

    protected function parsePlatform()
    {
        if ($this->matchUserAgent('arm')) {
            return 'ARM';
        } elseif ($this->matchUserAgent('WOW64|x64|win64|amd64|x86_64')) {
            return 'x64';
        } elseif ($this->matchUserAgent('i[0-9]86|i86pc')) {
            return 'x86';
        }

        return '';
    }


    /**
     * Returns the operating system family for the given operating system
     *
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
     * Returns the full name for the given short name
     *
     * @param      $os
     * @param bool $ver
     *
     * @return bool|string
     */
    public static function getNameFromId($os, $ver = false)
    {
        if (array_key_exists($os, self::$operatingSystems)) {
            $osFullName = self::$operatingSystems[$os];
            return trim($osFullName . " " . $ver);
        }
        return false;
    }
}
