<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser\Client;

use DeviceDetector\Parser\Client\Browser;
use \Spyc;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $client)
    {
        $browserParser = new Browser();
        $browserParser->setUserAgent($useragent);
        $this->assertEquals($client, $browserParser->parse());
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/browser.yml');
        return $fixtureData;
    }

    public function testGetAvailableBrowserFamilies()
    {
        $this->assertGreaterThan(5, Browser::getAvailableBrowserFamilies());
    }
}
