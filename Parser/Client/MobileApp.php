<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client;

/**
 * Class MobileApp
 *
 * Client parser for mobile app detection
 *
 * @package DeviceDetector\Parser\Client
 */
class MobileApp extends ClientParserAbstract
{
    protected $fixtureFile = 'regexes/client/mobile_apps.yml';
    protected $parserName = 'mobile app';

    /**
     * Known browsers mapped to their internal short codes
     *
     * @var array
     */
    protected static $availableBrowsers = array(
        'wechat' => 'WeChat',
        'weibo' => 'Sina Weibo',
        'zms' => 'Zaomengshe',
        'adm' => 'AndroidDownloadManager',
    );

    /**
     * Returns list of all available browsers
     * @return array
     */
    public static function getAvailableBrowsers()
    {
        return self::$availableBrowsers;
    }

    /**
     * @param $browserLabel
     * @return bool|string If false, "Unknown"
     */
    public static function getBrowserFamily($browserLabel)
    {
        return false;
    }

    public function parse()
    {
        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);
            if ($matches)
                break;
        }

        if (!$matches) {
            return null;
        }

        $name  = $this->buildByMatch($regex['name'], $matches);

        foreach (self::getAvailableBrowsers() AS $browserShort => $browserName) {
            if (strtolower($name) == strtolower($browserName)) {
                $version = (string) $this->buildVersion($regex['version'], $matches);
                return array(
                    'type'       => 'browser',
                    'name'       => $browserName,
                    'short_name' => $browserShort,
                    'version'    => $version,
                );
            }
        }

        // This Exception should never be thrown. If so a defined browser name is missing in $availableBrowsers
        throw new \Exception('Detected browser name was not found in $availableBrowsers'); // @codeCoverageIgnore
    }
}