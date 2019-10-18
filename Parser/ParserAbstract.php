<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser;

use DeviceDetector\Cache\StaticCache;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Cache\Cache;
use DeviceDetector\Yaml\Parser AS YamlParser;
use DeviceDetector\Yaml\Spyc;

/**
 * Class ParserAbstract
 *
 * @package DeviceDetector\Parser
 */
abstract class ParserAbstract
{
    /**
     * Holds the path to the yml file containing regexes
     * @var string
     */
    protected $fixtureFile;
    /**
     * Holds the internal name of the parser
     * Used for caching
     * @var string
     */
    protected $parserName;

    /**
     * Holds the user agent the should be parsed
     * @var string
     */
    protected $userAgent;

    /**
     * Holds an array with method that should be available global
     * @var array
     */
    protected $globalMethods;

    /**
     * Holds an array with regexes to parse, if already loaded
     * @var array
     */
    protected $regexList;

    /**
     * Indicates how deep versioning will be detected
     * if $maxMinorParts is 0 only the major version will be returned
     * @var int
     */
    protected static $maxMinorParts = 1;

    /**
     * Versioning constant used to set max versioning to major version only
     * Version examples are: 3, 5, 6, 200, 123, ...
     */

    const VERSION_TRUNCATION_MAJOR = 0;

    /**
     * Versioning constant used to set max versioning to minor version
     * Version examples are: 3.4, 5.6, 6.234, 0.200, 1.23, ...
     */
    const VERSION_TRUNCATION_MINOR = 1;

    /**
     * Versioning constant used to set max versioning to path level
     * Version examples are: 3.4.0, 5.6.344, 6.234.2, 0.200.3, 1.2.3, ...
     */
    const VERSION_TRUNCATION_PATCH = 2;

    /**
     * Versioning constant used to set versioning to build number
     * Version examples are: 3.4.0.12, 5.6.334.0, 6.234.2.3, 0.200.3.1, 1.2.3.0, ...
     */
    const VERSION_TRUNCATION_BUILD = 3;

    /**
     * Versioning constant used to set versioning to unlimited (no truncation)
     */
    const VERSION_TRUNCATION_NONE  = null;

    /**
     * @var Cache|\Doctrine\Common\Cache\CacheProvider
     */
    protected $cache;

    /**
     * @var YamlParser
     */
    protected $yamlParser;

    abstract public function parse();

    public function __construct($ua='')
    {
        $this->setUserAgent($ua);
    }

    /**
     * Set how DeviceDetector should return versions
     * @param int|null $type Any of the VERSION_TRUNCATION_* constants
     */
    public static function setVersionTruncation($type)
    {
        if (in_array($type, array(self::VERSION_TRUNCATION_BUILD,
                                 self::VERSION_TRUNCATION_NONE,
                                 self::VERSION_TRUNCATION_MAJOR,
                                 self::VERSION_TRUNCATION_MINOR,
                                 self::VERSION_TRUNCATION_PATCH))) {
            self::$maxMinorParts = $type;
        }
    }

    /**
     * Sets the user agent to parse
     *
     * @param string $ua  user agent
     */
    public function setUserAgent($ua)
    {
        $this->userAgent = $ua;
    }

    /**
     * Returns the internal name of the parser
     *
     * @return string
     */
    public function getName()
    {
        return $this->parserName;
    }

    /**
     * Returns the result of the parsed yml file defined in $fixtureFile
     *
     * @return array
     */
    protected function getRegexes()
    {
        if (empty($this->regexList)) {
            $cacheKey = 'DeviceDetector-'.DeviceDetector::VERSION.'regexes-'.$this->getName();
            $cacheKey = preg_replace('/([^a-z0-9_-]+)/i', '', $cacheKey);
            $this->regexList = $this->getCache()->fetch($cacheKey);
            if (empty($this->regexList)) {
                $this->regexList = $this->getYamlParser()->parseFile(
                    $this->getRegexesDirectory().DIRECTORY_SEPARATOR.$this->fixtureFile
                );
                $this->getCache()->save($cacheKey, $this->regexList);
            }
        }
        return $this->regexList;
    }

    /**
     * @return string
     */
    protected function getRegexesDirectory()
    {
        return dirname(__DIR__);
    }

    /**
     * Matches the useragent against the given regex
     *
     * @param string $regex
     * @return array|bool
     */
    protected function matchUserAgent($regex)
    {
        // only match if useragent begins with given regex or there is no letter before it
        $regex = '/(?:^|[^A-Z0-9\-_]|[^A-Z0-9\-]_|sprd-)(?:' . str_replace('/', '\/', $regex) . ')/i';

        if (preg_match($regex, $this->userAgent, $matches)) {
            return $matches;
        }

        return false;
    }

    /**
     * @param string $item
     * @param array $matches
     * @return string type
     */
    protected function buildByMatch($item, $matches)
    {
        for ($nb=1;$nb<=3;$nb++) {
            if (strpos($item, '$' . $nb) === false) {
                continue;
            }

            $replace = isset($matches[$nb]) ? $matches[$nb] : '';
            $item = trim(str_replace('$' . $nb, $replace, $item));
        }
        return $item;
    }

    /**
     * Builds the version with the given $versionString and $matches
     *
     * Example:
     * $versionString = 'v$2'
     * $matches = array('version_1_0_1', '1_0_1')
     * return value would be v1.0.1
     *
     * @param $versionString
     * @param $matches
     * @return mixed|string
     */
    protected function buildVersion($versionString, $matches)
    {
        $versionString = $this->buildByMatch($versionString, $matches);
        $versionString = str_replace('_', '.', $versionString);
        if (null !== self::$maxMinorParts && substr_count($versionString, '.') > self::$maxMinorParts) {
            $versionParts = explode('.', $versionString);
            $versionParts = array_slice($versionParts, 0, 1+self::$maxMinorParts);
            $versionString = implode('.', $versionParts);
        }
        return trim($versionString, ' .');
    }

    /**
     * Tests the useragent against a combination of all regexes
     *
     * All regexes returned by getRegexes() will be reversed and concated with '|'
     * Afterwards the big regex will be tested against the user agent
     *
     * Method can be used to speed up detections by making a big check before doing checks for every single regex
     *
     * @return bool
     */
    protected function preMatchOverall()
    {
        $regexes = $this->getRegexes();

        static $overAllMatch;

        $cacheKey = $this->parserName.DeviceDetector::VERSION.'-all';
        $cacheKey = preg_replace('/([^a-z0-9_-]+)/i', '', $cacheKey);

        if (empty($overAllMatch)) {
            $overAllMatch = $this->getCache()->fetch($cacheKey);
        }

        if (empty($overAllMatch)) {
            // reverse all regexes, so we have the generic one first, which already matches most patterns
            $overAllMatch = array_reduce(array_reverse($regexes), function ($val1, $val2) {
                if (!empty($val1)) {
                    return $val1.'|'.$val2['regex'];
                } else {
                    return $val2['regex'];
                }
            });
            $this->getCache()->save($cacheKey, $overAllMatch);
        }

        return $this->matchUserAgent($overAllMatch);
    }

    /**
     * Sets the Cache class
     *
     * @param Cache|\Doctrine\Common\Cache\CacheProvider $cache
     * @throws \Exception
     */
    public function setCache($cache)
    {
        if ($cache instanceof Cache ||
            (class_exists('\Doctrine\Common\Cache\CacheProvider') && $cache instanceof \Doctrine\Common\Cache\CacheProvider)) {
            $this->cache = $cache;
            return;
        }

        throw new \Exception('Cache not supported');
    }

    /**
     * Returns Cache object
     *
     * @return Cache|\Doctrine\Common\Cache\CacheProvider
     */
    public function getCache()
    {
        if (!empty($this->cache)) {
            return $this->cache;
        }

        return new StaticCache();
    }

    /**
     * Sets the YamlParser class
     *
     * @param YamlParser
     * @throws \Exception
     */
    public function setYamlParser($yamlParser)
    {
        if ($yamlParser instanceof YamlParser) {
            $this->yamlParser = $yamlParser;
            return;
        }

        throw new \Exception('Yaml Parser not supported');
    }

    /**
     * Returns YamlParser object
     *
     * @return YamlParser
     */
    public function getYamlParser()
    {
        if (!empty($this->yamlParser)) {
            return $this->yamlParser;
        }

        return new Spyc();
    }
}
