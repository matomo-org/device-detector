<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Tests\Cache;

use DeviceDetector\Cache\CacheFile;
use DeviceDetector\Cache\CacheStatic;

class CacheFileTests extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        CacheStatic::reset();
    }

    public function testSetNotPresent()
    {
        $cache = new CacheFile(sys_get_temp_dir());
        $this->assertNull($cache->get('NotExistingKey'));
    }

    public function testSetAndGet()
    {
        $cache = new CacheFile(sys_get_temp_dir());
        $this->assertTrue($cache->set('key', 'value'));
        CacheStatic::reset();
        $this->assertEquals('value', $cache->get('key'));
        $this->assertTrue($cache->set('key', 'value2'));
        CacheStatic::reset();
        $this->assertEquals('value2', $cache->get('key'));
    }

}
