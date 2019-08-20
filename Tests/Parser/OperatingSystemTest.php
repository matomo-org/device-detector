<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser;

use DeviceDetector\Parser\OperatingSystem;
use \Spyc;
use PHPUnit\Framework\TestCase;

class OperatingSystemTest extends TestCase
{
    static $osTested = [];

    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $os): void
    {
        $osParser = new OperatingSystem();
        $osParser->setUserAgent($useragent);

        // @todo remove me
        unset($os['short_name']);

        $this->assertEquals($os, $osParser->parse());
        self::$osTested[] = $os['name'];
    }

    public function getFixtures()
    {
        $fixtureData = Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/oss.yml');

        return $fixtureData;
    }

    /**
     * @dataProvider getAllOs
     */
    public function testOSInGroup($os): void
    {
        $familyOs = call_user_func_array('array_merge', OperatingSystem::getAvailableOperatingSystemFamilies());
        $this->assertContains($os, $familyOs);
    }

    public function getAllOs()
    {
        $allOs = OperatingSystem::getAvailableOperatingSystems();
        $allOs = array_map(function ($os) {
            return [$os];
        }, $allOs);

        return $allOs;
    }

    /**
     * @dataProvider getAllFamilyOs
     */
    public function testFamilyOSExists($os): void
    {
        $allOs = OperatingSystem::getAvailableOperatingSystems();
        $this->assertContains($os, $allOs);
    }

    public function getAllFamilyOs()
    {
        $allFamilyOs = call_user_func_array('array_merge', OperatingSystem::getAvailableOperatingSystemFamilies());
        $allFamilyOs = array_map(function ($os) {
            return [$os];
        }, $allFamilyOs);

        return $allFamilyOs;
    }

    public function testGetAvailableOperatingSystems(): void
    {
        $this->assertGreaterThan(70, OperatingSystem::getAvailableOperatingSystems());
    }

    public function testAllOperatingSystemsTested(): void
    {
        $allBrowsers = OperatingSystem::getAvailableOperatingSystems();
        $osNotTested = array_diff($allBrowsers, self::$osTested);
        $this->assertEmpty($osNotTested, 'Following browsers are not tested: ' . implode(', ', $osNotTested));
    }
}
