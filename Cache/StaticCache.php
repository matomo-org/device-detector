<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Cache;

/**
 * Class StaticCache
 *
 * Simple Cache that caches in a static property
 * (Speeds up multiple detections in one request)
 */
class StaticCache implements CacheInterface
{
    /**
     * Holds the static cache data
     * @var array
     */
    protected static $staticCache = [];

    /**
     * @inheritdoc
     */
    public function fetch($id)
    {
        return $this->contains($id) ? self::$staticCache[$id] : false;
    }

    /**
     * @inheritdoc
     */
    public function contains($id)
    {
        return isset(self::$staticCache[$id]) || \array_key_exists($id, self::$staticCache);
    }

    /**
     * @inheritdoc
     */
    public function save($id, $data, $lifeTime = 0)
    {
        self::$staticCache[$id] = $data;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        unset(self::$staticCache[$id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function flushAll()
    {
        self::$staticCache = [];

        return true;
    }
}
