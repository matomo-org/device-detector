<?php

declare(strict_types=1);

namespace DeviceDetector\Cache;


use Illuminate\Support\Facades\Cache;

class LaravelCache implements CacheInterface
{

    public function fetch(string $id)
    {
        return Cache::get($id);
    }

    public function contains(string $id): bool
    {
        return Cache::has($id);
    }

    public function save(string $id, $data, int $lifeTime = 0): bool
    {
        return Cache::put($id, $data, \func_num_args() < 3 ? null : $lifeTime);
    }

    public function delete(string $id): bool
    {
        return Cache::forget($id);
    }

    public function flushAll(): bool
    {
        return Cache::flush();
    }
}
