<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser;

use \Spyc;
use DeviceDetector\Parser\OperatingSystem;
use PHPUnit\Framework\TestCase;

class OperatingSystemTest extends TestCase
{
    protected static $osTested = [];

    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $os): void
    {
        $osParser = new OperatingSystem();
        $osParser->setUserAgent($useragent);
        $this->assertEquals($os, $osParser->parse());
        self::$osTested[] = $os['name'];
    }

    public function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/oss.yml');

        return $fixtureData;
    }

    /**
     * @dataProvider getAllOs
     */
    public function testOSInGroup(string $os): void
    {
        $familyOs = \call_user_func_array('array_merge', \array_values(OperatingSystem::getAvailableOperatingSystemFamilies()));
        $this->assertContains($os, $familyOs);
    }

    public function getAllOs(): array
    {
        $allOs = \array_keys(OperatingSystem::getAvailableOperatingSystems());
        $allOs = \array_map(static function ($os) {
            return [$os];
        }, $allOs);

        return $allOs;
    }

    /**
     * @dataProvider getAllFamilyOs
     */
    public function testFamilyOSExists(string $os): void
    {
        $allOs = \array_keys(OperatingSystem::getAvailableOperatingSystems());
        $this->assertContains($os, $allOs);
    }

    public function getAllFamilyOs(): array
    {
        $allFamilyOs = \call_user_func_array('array_merge', \array_values(OperatingSystem::getAvailableOperatingSystemFamilies()));
        $allFamilyOs = \array_map(static function ($os) {
            return [$os];
        }, $allFamilyOs);

        return $allFamilyOs;
    }

    public function testGetAvailableOperatingSystems(): void
    {
        $this->assertGreaterThan(70, OperatingSystem::getAvailableOperatingSystems());
    }

    /**
     * @dataProvider getNameFromIds
     */
    public function testGetNameFromId(string $os, string $version, ?string $expected): void
    {
        $this->assertEquals($expected, OperatingSystem::getNameFromId($os, $version));
    }

    public function getNameFromIds(): array
    {
        return [
            ['DEB', '4.5', 'Debian 4.5'],
            ['WRT', '', 'Windows RT'],
            ['WIN', '98', 'Windows 98'],
            ['XXX', '4.5', null],
        ];
    }

    public function testAllOperatingSystemsTested(): void
    {
        $allBrowsers = OperatingSystem::getAvailableOperatingSystems();
        $osNotTested = \array_diff($allBrowsers, self::$osTested);
        $this->assertEmpty($osNotTested, 'Following browsers are not tested: ' . \implode(', ', $osNotTested));
    }
}
