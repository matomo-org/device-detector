<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

require __DIR__ . '/../vendor/autoload.php';

class DeviceDetectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getFixtures
     */
    public function testParse($fixtureData)
    {
        $ua = $fixtureData['user_agent'];
        $uaInfo = DeviceDetector::getInfoFromUserAgent($ua);
        $this->assertEquals($fixtureData, $uaInfo);
    }

    public function getFixtures()
    {
        $fixtures = array();
        $fixtureFiles = glob(realpath(dirname(__FILE__)) . '/fixtures/*.yml');
        foreach ($fixtureFiles AS $fixturesPath) {
            $typeFixtures = Spyc::YAMLLoad($fixturesPath);
            $deviceType = str_replace('_', ' ', substr(basename($fixturesPath), 0, -4));
            if (in_array($deviceType, DeviceDetector::$deviceTypes) || $deviceType == 'unknown') {
                $fixtures = array_merge(array_map(function($elem) {return array($elem);}, $typeFixtures), $fixtures);
            }
        }
        return $fixtures;
    }

    /**
     * @dataProvider getAllOs
     */
    public function testOSInGroup($os)
    {
        $familyOs = call_user_func_array('array_merge', DeviceDetector::$osFamilies);
        $this->assertContains($os, $familyOs);
    }

    public function getAllOs()
    {
        $allOs = array_keys(DeviceDetector::$operatingSystems);
        $allOs = array_map(function($os){ return array($os); }, $allOs);
        return $allOs;
    }
}
