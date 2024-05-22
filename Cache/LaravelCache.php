<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Cache;

use Illuminate\Support\Facades\Cache;

class LaravelCache implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function fetch($id)
    {
        return Cache::get($id);
    }

    /**
     * @inheritDoc
     */
    public function contains($id)
    {
        return Cache::has($id);
    }

    /**
     * @inheritDoc
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return Cache::put($id, $data, \func_num_args() < 3 ? null : $lifeTime);
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return Cache::forget($id);
    }

    /**
     * @inheritDoc
     */
    public function flushAll()
    {
        return Cache::flush();
    }
}
