<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser\Device;

use DeviceDetector\Parser\Device\DeviceParserAbstract;
use PHPUnit\Framework\TestCase;

class DeviceParserAbstractTest extends TestCase
{
    public function testGetAvailableDeviceTypes()
    {
        $available = DeviceParserAbstract::getAvailableDeviceTypes();
        $this->assertGreaterThan(5, count($available));
        $this->assertContains('desktop', array_keys($available));
    }

    public function testGetAvailableDeviceTypeNames()
    {
        $available = DeviceParserAbstract::getAvailableDeviceTypeNames();
        $this->assertGreaterThan(5, count($available));
        $this->assertContains('desktop', $available);
    }

    public function testGetFullName()
    {
        $this->assertEquals('', DeviceParserAbstract::getFullName('Invalid'));
        $this->assertEquals('Asus', DeviceParserAbstract::getFullName('AU'));
        $this->assertEquals('Google', DeviceParserAbstract::getFullName('GO'));
    }
}
