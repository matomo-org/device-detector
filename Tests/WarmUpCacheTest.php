<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests;

use DeviceDetector\Cache\StaticCache;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use PHPUnit\Framework\TestCase;
use Spyc;

class WarmUpCacheTest extends TestCase
{
    public function testParsingDoesNotStoreAnythingAfterWarmUp(): void
    {
        $cache = new class() extends StaticCache {
            /**
             * @var array<string>
             */
            public $saved = [];

            /**
             * @param mixed $data
             */
            public function save(string $id, $data, int $lifeTime = 0): bool
            {
                $this->saved[] = $id;

                return parent::save($id, $data, $lifeTime);
            }
        };
        $cache->flushAll();

        $dd = new DeviceDetector();
        $dd->setCache($cache);
        $dd->warmUpCache();

        $this->assertNotEmpty($cache->saved);
        $cache->saved = [];

        foreach (\glob(__DIR__ . '/fixtures/*.yml') ?: [] as $fixtureFile) {
            foreach (\array_slice(Spyc::YAMLLoad($fixtureFile), 0, 50) as $fixture) {
                $dd = new DeviceDetector($fixture['user_agent'], ClientHints::factory($fixture['headers'] ?? []));
                $dd->setCache($cache);
                $dd->parse();
            }
        }

        $this->assertSame([], $cache->saved, 'Cache entries not created by warmUpCache()');
    }
}
