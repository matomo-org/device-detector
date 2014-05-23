<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Parser;

use DeviceDetector\Cache\CacheInterface;
use DeviceDetector\Cache\CacheStatic;
use \Spyc;

/**
 * Class ParserAbstract
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
     * @var CacheInterface
     */
    protected $cache;

    abstract public function parse();

    public function __construct($ua='')
    {
        $this->setUserAgent($ua);
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
        $cacheKey = 'DeviceDetector-regexes-'.$this->getName();
        $regexList = $this->getCache()->get($cacheKey);
        if (empty($regexList)) {
            $regexList = Spyc::YAMLLoad(dirname(__DIR__).DIRECTORY_SEPARATOR.$this->fixtureFile);
            $this->getCache()->set($cacheKey, $regexList);
        }
        return $regexList;
    }

    /**
     * Matches the useragent against the given regex
     *
     * @param $regex
     * @return bool
     */
    protected function matchUserAgent($regex)
    {
        // only match if useragent begins with given regex or there is no letter before it
        $regex = '/(?:^|[^A-Z_-])(?:' . str_replace('/', '\/', $regex) . ')/i';

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
    protected function buildVersion($versionString, $matches) {
        $versionString = $this->buildByMatch($versionString, $matches);

        $versionString = str_replace('_', '.', $versionString);

        return $versionString;
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

        if (empty($overAllMatch)) {
            $overAllMatch = $this->getCache()->get($this->parserName.'-all');
        }

        if (empty($overAllMatch)) {
            // reverse all regexes, so we have the generic one first, which already matches most patterns
            $overAllMatch = array_reduce(array_reverse($regexes), function($val1, $val2) {
                if (!empty($val1)) {
                    return $val1.'|'.$val2['regex'];
                } else {
                    return $val2['regex'];
                }
            });
            $this->getCache()->set($this->parserName.'-all', $overAllMatch);
        }

        return $this->matchUserAgent($overAllMatch);
    }

    /**
     * Sets the Cache class
     *
     * Note: The given class needs to have a 'get' and 'set' method to be used
     *
     * @param $cache
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns Cache object
     *
     * @return CacheInterface
     */
    public function getCache()
    {
        if (!empty($this->cache)) {
            return $this->cache;
        }

        return new CacheStatic();
    }

}