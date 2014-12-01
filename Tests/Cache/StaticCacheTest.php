<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Cache;

use DeviceDetector\Cache\StaticCache;

class StaticCacheTests extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $cache = new StaticCache();
        $cache->flushAll();
    }

    public function testSetNotPresent()
    {
        $cache = new StaticCache();
        $this->assertFalse($cache->fetch('NotExistingKey'));
    }

    public function testSetAndGet()
    {
        $cache = new StaticCache();
        $cache->save('key', 'value');
        $this->assertEquals('value', $cache->fetch('key'));
        $cache->save('key', 'value2');
        $this->assertEquals('value2', $cache->fetch('key'));

        $cache->flushAll();

        $this->assertFalse($cache->fetch('key'));
    }

}
