<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Cache;

/**
 * Class CacheStatic
 *
 * Simple Cache that caches in a static property
 * (Speeds up multiple detections in one request)
 *
 * @package DeviceDetector\Cache
 */
class CacheStatic implements CacheInterface
{
    /**
     * Holds the static cache data
     * @var array
     */
    static protected $staticCache = array();

    public function set($key, $value)
    {
        self::$staticCache[$key] = $value;
    }

    public function get($key)
    {
        if (array_key_exists($key, self::$staticCache)) {
            return self::$staticCache[$key];
        }

        return null;
    }

    /**
     * Resets the static cache
     */
    public static function reset()
    {
        self::$staticCache = array();
    }
}