<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Cache;

/**
 * Interface CacheInterface
 *
 * Simple Interface to provide basic caching methods
 *
 * @package DeviceDetector\Cache
 */
interface CacheInterface
{
    /**
     * Sets data for $key in cache to $value
     *
     * @param string $key
     * @param $value
     *
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Get data from cache for given $key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

}