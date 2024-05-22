<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Cache;

interface CacheInterface
{
    /**
     * @param string $id
     *
     * @return mixed
     */
    public function fetch($id);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function contains($id);

    /**
     * @param string $id
     * @param mixed  $data
     * @param int    $lifeTime
     *
     * @return bool
     */
    public function save($id, $data, $lifeTime = 0);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function delete($id);

    /**
     * @return bool
     */
    public function flushAll();
}
