<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Cache;

use DeviceDetector\Cache\PSR6Bridge;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;
use MatthiasMullie\Scrapbook\Psr6\Pool;
use PHPUnit\Framework\TestCase;

class PSR6CacheTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('\MatthiasMullie\Scrapbook\Adapters\MemoryStore')) {
            $this->markTestSkipped('class \MatthiasMullie\Scrapbook\Adapters\MemoryStore required for tests');
            return;
        }

        $cache = new PSR6Bridge(new Pool(new MemoryStore()));
        $cache->flushAll();
    }

    public function testSetNotPresent(): void
    {
        $cache = new PSR6Bridge(new Pool(new MemoryStore()));
        $this->assertFalse($cache->fetch('NotExistingKey'));
    }

    public function testSetAndGet(): void
    {
        $cache = new PSR6Bridge(new Pool(new MemoryStore()));

        // add entry
        $cache->save('key', 'value');
        $this->assertEquals('value', $cache->fetch('key'));

        // change entry
        $cache->save('key', 'value2');
        $this->assertEquals('value2', $cache->fetch('key'));

        // remove entry
        $cache->delete('key');
        $this->assertFalse($cache->fetch('key'));

        // flush all entries
        $cache->save('key', 'value2');
        $cache->save('key3', 'value2');
        $cache->flushAll();
        $this->assertFalse($cache->fetch('key'));
        $this->assertFalse($cache->fetch('key3'));
    }
}
