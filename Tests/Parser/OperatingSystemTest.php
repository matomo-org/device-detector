<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
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
}
