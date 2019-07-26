<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
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
class StaticCache implements Cache
{
    /**
     * Holds the static cache data
     * @var array
     */
    protected static $staticCache = [];

    public function fetch($id)
    {
        return $this->contains($id) ? self::$staticCache[$id] : false;
    }

    public function contains($id): bool
    {
        return isset(self::$staticCache[$id]) || array_key_exists($id, self::$staticCache);
    }

    public function save($id, $data, $lifeTime = 0): bool
    {
        self::$staticCache[$id] = $data;

        return true;
    }

    public function delete($id): bool
    {
        unset(self::$staticCache[$id]);

        return true;
    }

    public function flushAll(): bool
    {
        self::$staticCache = [];

        return true;
    }
}
