<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Cache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

class PSR16Bridge implements CacheInterface
{
    /**
     * @var PsrCacheInterface
     */
    private $cache;

    /**
     * PSR16Bridge constructor.
     * @param PsrCacheInterface $cache
     */
    public function __construct(PsrCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function fetch($id)
    {
        return $this->cache->get($id, false);
    }

    /**
     * @inheritDoc
     */
    public function contains($id)
    {
        return $this->cache->has($id);
    }

    /**
     * @inheritDoc
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->cache->set($id, $data, \func_num_args() < 3 ? null : $lifeTime);
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
        return $this->cache->clear();
    }
}
