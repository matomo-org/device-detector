<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Cache;

use Doctrine\Common\Cache\CacheProvider;

/**
 * Class StaticCache
 *
 * Simple Cache that caches in a static property
 * (Speeds up multiple detections in one request)
 *
 * @package DeviceDetector\Cache
 */
class StaticCache extends CacheProvider
{
    /**
     * Holds the static cache data
     * @var array
     */
    static protected $staticCache = array();

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return $this->doContains($id) ? self::$staticCache[$id] : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return isset(self::$staticCache[$id]) || array_key_exists($id, self::$staticCache);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        self::$staticCache[$id] = $data;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        unset(self::$staticCache[$id]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        self::$staticCache = array();
        return true;
    }
}