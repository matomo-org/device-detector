<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Cache;

/**
 * Class CacheMemcache
 *
 * Memcache adapter for caching in local memcache
 *
 * @package DeviceDetector\Cache
 */
class CacheMemcache implements CacheInterface
{
    /**
     * Holds the memcache instance
     * @var \Memcache
     */
    static protected $memcache = null;

    public function __construct($server='localhost', $port=11211)
    {
        if (empty(self::$memcache)) {
            self::$memcache = new \Memcache();
            self::$memcache->connect($server, $port) or die ("Could not connect");
        }
    }

    public function set($key, $value)
    {
        self::$memcache->set($key, $value);
    }

    public function get($key)
    {
        return self::$memcache->get($key);
    }

    public static function reset()
    {
        self::$memcache->flush();
    }
}