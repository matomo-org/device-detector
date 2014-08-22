<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Cache;

use DeviceDetector\Cache\CacheFile;
use DeviceDetector\Cache\CacheStatic;

class CacheFileTests extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (getenv('TRAVIS_PHP_VERSION') == 'hhvm') {
            $this->markTestSkipped();
        }
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

    public function testSetInvalidPath()
    {
        $cache = new CacheFile('invalidDir');
        $this->assertFalse($cache->set('key', 'value'));
    }

    public function testSetInvalidKey()
    {
        $cache = new CacheFile(sys_get_temp_dir());
        $this->assertFalse($cache->set('', 'value'));
    }

    /**
     * @expectedException \Exception
     */
    public function testSetInvalidObject()
    {
        $cache = new CacheFile(sys_get_temp_dir());
        $this->assertFalse($cache->set('key', new \stdClass()));
    }

}
