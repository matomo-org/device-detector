<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Cache;

interface Cache
{
    /**
     * @param string $id
     * @return mixed
     */
    public function fetch($id);

    /**
     * @param $id
     * @return bool
     */
    public function contains($id): bool;

    /**
     * @param string $id
     * @param mixed  $data
     * @param int    $lifeTime
     * @return bool
     */
    public function save($id, $data, $lifeTime = 0): bool;

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id): bool;

    /**
     * @return bool
     */
    public function flushAll(): bool;
}
