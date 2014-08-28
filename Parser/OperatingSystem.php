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
        'DEB' => 'Debian',
        'DFB' => 'DragonFly',
        'FED' => 'Fedora',
        'FOS' => 'Firefox OS',
        'BSD' => 'FreeBSD',
        'GNT' => 'Gentoo',
        'GTV' => 'Google TV',
        'HPX' => 'HP-UX',
        'HAI' => 'Haiku OS',
        'IRI' => 'IRIX',
        'INF' => 'Inferno',
        'KNO' => 'Knoppix',
        'KBT' => 'Kubuntu',
        'LIN' => 'GNU/Linux',
        'LBT' => 'Lubuntu',
        'VLN' => 'VectorLinux',
        'MAC' => 'Mac',
        'MDR' => 'Mandriva',
        'SMG' => 'MeeGo',
        'MIN' => 'Mint',
        'NBS' => 'NetBSD',
        'WII' => 'Nintendo',
        'NDS' => 'Nintendo Mobile',
        'OS2' => 'OS/2',
        'T64' => 'OSF1',
        'OBS' => 'OpenBSD',
        'PSP' => 'PlayStation Portable',
        'PS3' => 'PlayStation',
        'RHT' => 'Red Hat',
        'ROS' => 'RISC OS',
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
        'TIZ' => 'Tizen',
        'UBT' => 'Ubuntu',
        'WTV' => 'WebTV',
        'WIN' => 'Windows',
        'W2K' => 'Windows 2000',
        'W31' => 'Windows 3.1',
        'WI7' => 'Windows 7',
        'WI8' => 'Windows 8',
        'W81' => 'Windows 8.1',
        'W95' => 'Windows 95',
        'W98' => 'Windows 98',
        'WCE' => 'Windows CE',
        'WME' => 'Windows ME',
        'WMO' => 'Windows Mobile',
        'WNT' => 'Windows NT',
        'WPH' => 'Windows Phone',
        'WRT' => 'Windows RT',
        'WS3' => 'Windows Server 2003',
        'WVI' => 'Windows Vista',
        'WXP' => 'Windows XP',
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
        'Android'               => array('AND'),
        'AmigaOS'               => array('AMG'),
        'Apple TV'              => array('ATV'),
        'BlackBerry'            => array('BLB', 'QNX'),
        'Brew'                  => array('BMP'),
        'BeOS'                  => array('BEO', 'HAI'),
        'Chrome OS'             => array('COS'),
        'Firefox OS'            => array('FOS'),
        'Gaming Console'        => array('WII', 'PS3'),
        'Google TV'             => array('GTV'),
        'IBM'                   => array('OS2'),
        'iOS'                   => array('IOS'),
        'RISC OS'               => array('ROS'),
        'GNU/Linux'             => array('LIN', 'ARL', 'DEB', 'KNO', 'MIN', 'UBT', 'KBT', 'XBT', 'LBT', 'FED', 'RHT', 'VLN', 'MDR', 'GNT', 'SAB', 'SLW', 'SSE', 'CES', 'BTR', 'YNS', 'SAF'),
        'Mac'                   => array('MAC'),
        'Mobile Gaming Console' => array('PSP', 'NDS', 'XBX'),
        'Other Mobile'          => array('WOS', 'POS', 'SBA', 'TIZ', 'SMG'),
        'Symbian'               => array('SYM', 'SYS', 'SY3', 'S60', 'S40'),
        'Unix'                  => array('SOS', 'AIX', 'HPX', 'BSD', 'NBS', 'OBS', 'DFB', 'SYL', 'IRI', 'T64', 'INF'),
        'WebTV'                 => array('WTV'),
        'Windows'               => array('WI7', 'WI8', 'W81', 'WVI', 'WS3', 'WXP', 'W2K', 'WNT', 'WME', 'W98', 'W95', 'WRT', 'W31', 'WIN'),
        'Windows Mobile'        => array('WPH', 'WMO', 'WCE')
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
            if ($matches)
                break;
        }

        if (!$matches)
            return $return;

        $name  = $this->buildByMatch($osRegex['name'], $matches);
        $short = 'UNK';

        foreach (self::$operatingSystems AS $osShort => $osName) {
            if (strtolower($name) == strtolower($osName)) {
                $name  = $osName;
                $short = $osShort;
            }
        }

        $return = array(
            'name'       => $name,
            'short_name' => $short,
            'version'    => $this->buildVersion($osRegex['version'], $matches)
        );

        if (in_array($return['name'], self::$operatingSystems)) {
            $return['short_name'] = array_search($return['name'], self::$operatingSystems);
        }

        return $return;
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
            if (in_array($os, self::$osFamilies['Windows'])) {
                return $osFullName;
            } else {
                return trim($osFullName . " " . $ver);
            }
        }
        return false;
    }
}
