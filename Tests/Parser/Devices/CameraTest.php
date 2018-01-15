<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser\Device;

use DeviceDetector\Parser\Device\Camera;
use \Spyc;
use PHPUnit\Framework\TestCase;

class CameraTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $device)
    {
        $consoleParser = new Camera;
        $consoleParser->setUserAgent($useragent);
        $this->assertTrue($consoleParser->parse());
        $this->assertEquals($device['type'], $consoleParser->getDeviceType());
        $this->assertEquals($device['brand'], $consoleParser->getBrand());
        $this->assertEquals($device['model'], $consoleParser->getModel());
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/camera.yml');
        return $fixtureData;
    }
}
