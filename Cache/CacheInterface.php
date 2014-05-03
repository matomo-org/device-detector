<?php

namespace DeviceDetector\Cache;

interface CacheInterface {

    public function set($key, $value);

    public function get($key);

}