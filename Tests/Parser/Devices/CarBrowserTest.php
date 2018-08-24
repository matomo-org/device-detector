<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser\Device;

use DeviceDetector\Parser\Device\CarBrowser;
use \Spyc;
use PHPUnit\Framework\TestCase;

class CarBrowserTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $device)
    {
        $consoleParser = new CarBrowser();
        $consoleParser->setUserAgent($useragent);
        $this->assertTrue($consoleParser->parse());
        $this->assertEquals($device['type'], $consoleParser->getDeviceType());
        $this->assertEquals($device['brand'], $consoleParser->getBrand());
        $this->assertEquals($device['model'], $consoleParser->getModel());
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/car_browser.yml');
        return $fixtureData;
    }
}
