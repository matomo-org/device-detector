<?php

namespace DeviceDetector\Parser;

use DeviceDetector\Cache\CacheInterface;
use DeviceDetector\Cache\CacheStatic;
use \Spyc;

abstract class ParserAbstract {

    protected $fixtureFile;
    protected $parserName;
    protected $userAgent;

    /**
     * @var CacheInterface
     */
    protected $cache;

    abstract public function parse();

    public function __construct($ua='')
    {
        $this->setUserAgent($ua);
    }

    public function setUserAgent($ua)
    {
        $this->userAgent = $ua;
    }

    public function getName()
    {
        return $this->parserName;
    }

    protected function getRegexes()
    {
        $regexList = $this->getCache()->get($this->parserName);
        if (empty($regexList)) {
            $regexList = Spyc::YAMLLoad($this->fixtureFile);
            $this->getCache()->set($this->parserName, $regexList);
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


    protected function buildVersion($versionString, $matches) {
        $versionString = $this->buildByMatch($versionString, $matches);

        $versionString = str_replace('_', '.', $versionString);

        return $versionString;
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