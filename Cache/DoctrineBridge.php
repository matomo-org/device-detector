<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Cache;

use Doctrine\Common\Cache\CacheProvider ;

class DoctrineBridge implements Cache
{
    /**
     * @var CacheProvider
     */
    private $pool;

    /**
     * @param CacheProvider $cache
     */
    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function fetch($id)
    {
        return $this->cache->fetch($id);
    }

    /**
     * @inheritDoc
     */
    public function contains($id)
    {
        return $this->cache->contains($id);
    }

    /**
     * @inheritDoc
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->cache->save($id, $data, $lifeTime);
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return $this->cache->delete($id);
    }

    /**
     * @inheritDoc
     */
    public function flushAll()
    {
        return $this->cache->flushAll();
    }
}
