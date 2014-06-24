<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Tests\Cache;

use DeviceDetector\Cache\CacheMemcache;

class CacheMemcacheTests extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $cache = new CacheMemcache();
        CacheMemcache::reset();
    }

    public function testSetNotPresent()
    {
        $cache = new CacheMemcache();
        $this->assertFalse($cache->get('NotExistingKey'));
    }

    public function testSetAndGet()
    {
        $cache = new CacheMemcache();
        $cache->set('key', 'value');
        $this->assertEquals('value', $cache->get('key'));
        $cache->set('key', 'value2');
        $this->assertEquals('value2', $cache->get('key'));
    }

}
