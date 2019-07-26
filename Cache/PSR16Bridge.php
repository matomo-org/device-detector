<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Cache;

use Psr\SimpleCache\CacheInterface;

class PSR16Bridge implements Cache
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * PSR16Bridge constructor.
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
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
    public function contains($id): bool
    {
        return $this->cache->has($id);
    }

    /**
     * @inheritDoc
     */
    public function save($id, $data, $lifeTime = 0): bool
    {
        return $this->cache->set($id, $data, func_num_args() < 3 ? null : $lifeTime);
    }

    /**
     * @inheritDoc
     */
    public function delete($id): bool
    {
        return $this->cache->delete($id);
    }

    /**
     * @inheritDoc
     */
    public function flushAll(): bool
    {
        return $this->cache->clear();
    }
}
