<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Tests\Cache;

use DeviceDetector\Cache\CacheStatic;

class CacheStaticTests extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        CacheStatic::reset();
    }

    public function testSetNotPresent()
    {
        $cache = new CacheStatic();
        $this->assertNull($cache->get('NotExistingKey'));
    }

    public function testSetAndGet()
    {
        $cache = new CacheStatic();
        $cache->set('key', 'value');
        $this->assertEquals('value', $cache->get('key'));
        $cache->set('key', 'value2');
        $this->assertEquals('value2', $cache->get('key'));

        CacheStatic::reset();

        $this->assertNull($cache->get('key'));
    }

}
