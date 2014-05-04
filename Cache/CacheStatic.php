<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Cache;

class CacheStatic implements CacheInterface
{
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

}