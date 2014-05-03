<?php

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