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
use PHPUnit\Framework\TestCase;

class OperatingSystemTest extends TestCase
{
    static $osTested = array();

    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $os)
    {
        $osParser = new OperatingSystem();
        $osParser->setUserAgent($useragent);
        $this->assertEquals($os, $osParser->parse());
        self::$osTested[] = $os['short_name'];
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/oss.yml');
        return $fixtureData;
    }

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

    /**
     * @dataProvider getAllFamilyOs
     */
    public function testFamilyOSExists($os)
    {
        $allOs = array_keys(OperatingSystem::getAvailableOperatingSystems());
        $this->assertContains($os, $allOs);
    }

    public function getAllFamilyOs()
    {
        $allFamilyOs = call_user_func_array('array_merge', OperatingSystem::getAvailableOperatingSystemFamilies());
        $allFamilyOs = array_map(function($os){ return array($os); }, $allFamilyOs);
        return $allFamilyOs;
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
            array('WRT', '', 'Windows RT'),
            array('WIN', '98', 'Windows 98'),
            array('XXX', '4.5', false),
        );
    }

    public function testAllOperatingSystemsTested()
    {
        $allBrowsers = array_keys(OperatingSystem::getAvailableOperatingSystems());
        $osNotTested = array_diff($allBrowsers, self::$osTested);
        $this->assertEmpty($osNotTested, 'Following browsers are not tested: '.implode(', ', $osNotTested));
    }
}
