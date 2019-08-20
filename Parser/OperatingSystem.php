<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
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
     * Known operating systems
     *
     * @var array
     */
    protected static $operatingSystems = [
        'AIX',
        'Android',
        'AmigaOS',
        'Apple TV',
        'Arch Linux',
        'BackTrack',
        'Bada',
        'BeOS',
        'BlackBerry OS',
        'BlackBerry Tablet OS',
        'Brew',
        'CentOS',
        'Chrome OS',
        'CyanogenMod',
        'Debian',
        'DragonFly',
        'Fedora',
        'Firefox OS',
        'Fire OS',
        'FreeBSD',
        'Gentoo',
        'Google TV',
        'HP-UX',
        'Haiku OS',
        'IRIX',
        'Inferno',
        'KaiOS',
        'Knoppix',
        'Kubuntu',
        'GNU/Linux',
        'Lubuntu',
        'VectorLinux',
        'Mac',
        'Maemo',
        'Mandriva',
        'MeeGo',
        'MocorDroid',
        'Mint',
        'MildWild',
        'MorphOS',
        'NetBSD',
        'MTK / Nucleus',
        'Nintendo',
        'Nintendo Mobile',
        'OS/2',
        'OSF1',
        'OpenBSD',
        'Ordissimo',
        'PlayStation Portable',
        'PlayStation',
        'Red Hat',
        'RISC OS',
        'Remix OS',
        'RazoDroiD',
        'Sabayon',
        'SUSE',
        'Sailfish OS',
        'Slackware',
        'Solaris',
        'Syllable',
        'Symbian',
        'Symbian OS',
        'Symbian OS Series 40',
        'Symbian OS Series 60',
        'Symbian^3',
        'ThreadX',
        'Tizen',
        'TmaxOS',
        'Ubuntu',
        'WebTV',
        'Windows',
        'Windows CE',
        'Windows IoT',
        'Windows Mobile',
        'Windows Phone',
        'Windows RT',
        'Xbox',
        'Xubuntu',
        'YunOs',
        'iOS',
        'palmOS',
        'webOS',
    ];

    /**
     * Operating system families mapped to the associated operating systems
     *
     * @var array
     */
    protected static $osFamilies = [
        'Android'               => [
            'Android', 'CyanogenMod', 'Fire OS',
            'Remix OS', 'RazoDroiD', 'MildWild',
            'MocorDroid', 'YunOs',
        ],
        'AmigaOS'               => ['AmigaOS', 'MorphOS'],
        'Apple TV'              => ['Apple TV'],
        'BlackBerry'            => ['BlackBerry OS', 'BlackBerry Tablet OS'],
        'Brew'                  => ['Brew'],
        'BeOS'                  => ['BeOS', 'Haiku OS'],
        'Chrome OS'             => ['Chrome OS'],
        'Firefox OS'            => ['Firefox OS', 'KaiOS'],
        'Gaming Console'        => ['Nintendo', 'PlayStation'],
        'Google TV'             => ['Google TV'],
        'IBM'                   => ['OS/2'],
        'iOS'                   => ['iOS'],
        'RISC OS'               => ['RISC OS'],
        'GNU/Linux'             => [
            'GNU/Linux', 'Arch Linux', 'Debian', 'Knoppix', 'Mint', 'Ubuntu', 'Kubuntu', 'Xubuntu', 'Lubuntu', 'Fedora',
            'Red Hat', 'VectorLinux', 'Mandriva', 'Gentoo', 'Sabayon', 'Slackware', 'SUSE', 'CentOS', 'BackTrack',
            'Sailfish OS', 'Ordissimo', 'TmaxOS'
        ],
        'Mac'                   => ['Mac'],
        'Mobile Gaming Console' => ['PlayStation Portable', 'Nintendo Mobile', 'Xbox'],
        'Real-time OS'          => ['MTK / Nucleus', 'ThreadX'],
        'Other Mobile'          => ['webOS', 'palmOS', 'Bada', 'Tizen', 'MeeGo', 'Maemo'],
        'Symbian'               => [
            'Symbian', 'Symbian OS', 'Symbian^3', 'Symbian OS Series 60', 'Symbian OS Series 40',
        ],
        'Unix'                  => [
            'Solaris', 'AIX', 'HP-UX', 'FreeBSD', 'NetBSD', 'OpenBSD',
            'DragonFly', 'Syllable', 'IRIX', 'OSF1', 'Inferno',
        ],
        'WebTV'                 => ['WebTV'],
        'Windows'               => ['Windows'],
        'Windows Mobile'        => ['Windows Phone', 'Windows Mobile', 'Windows CE', 'Windows RT', 'Windows IoT'],
    ];

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

        $name = $this->buildByMatch($osRegex['name'], $matches);

        foreach (self::$operatingSystems as $osName) {
            if (strtolower($name) !== strtolower($osName)) {
                continue;
            }

            $name = $osName;
        }

        $return = [
            'name'     => $name,
            'version'  => $this->buildVersion((string) $osRegex['version'], $matches),
            'platform' => $this->parsePlatform(),
        ];

        return $return;
    }

    /**
     * Returns the operating system family for the given operating system
     *
     * @param string $osLabel
     *
     * @return string|null If null, "Unknown"
     */
    public static function getOsFamily(string $osLabel): ?string
    {
        foreach (self::$osFamilies as $family => $labels) {
            if (in_array($osLabel, $labels)) {
                return (string) $family;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    protected function parsePlatform(): string
    {
        if ($this->matchUserAgent('arm')) {
            return 'ARM';
        }

        if ($this->matchUserAgent('WOW64|x64|win64|amd64|x86_64')) {
            return 'x64';
        }

        if ($this->matchUserAgent('i[0-9]86|i86pc')) {
            return 'x86';
        }

        return '';
    }
}
