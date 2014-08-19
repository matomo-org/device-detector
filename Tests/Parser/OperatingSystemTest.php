<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser;

use DeviceDetector\Parser\OperatingSystem;
use \Spyc;

class OperatingSystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getAllOs
     */
    public function testOSInGroup($os)
    {
        $familyOs = call_user_func_array('array_merge', OperatingSystem::getAvailableOperatingSystemFamilies());
        $this->assertContains($os, $familyOs);
    }

    public function getAllOs()
    {
        $allOs = array_keys(OperatingSystem::getAvailableOperatingSystems());
        $allOs = array_map(function($os){ return array($os); }, $allOs);
        return $allOs;
    }

    public function testGetAvailableOperatingSystems()
    {
        $this->assertGreaterThan(70, OperatingSystem::getAvailableOperatingSystems());
    }

    /**
     * @dataProvider getNameFromIds
     */
    public function testGetNameFromId($os, $version, $expected)
    {
        $this->assertEquals($expected, OperatingSystem::getNameFromId($os, $version));
    }

    public function getNameFromIds()
    {
        return array(
            array('DEB', '4.5', 'Debian 4.5'),
            array('W98', '', 'Windows 98'),
            array('W98', '98', 'Windows 98'),
            array('XXX', '4.5', false),
        );
    }
}
