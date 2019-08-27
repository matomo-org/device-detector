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
use PHPUnit\Framework\TestCase;

class BrowserTest extends TestCase
{
    static $browsersTested = array();

    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $client)
    {
        $browserParser = new Browser();
        $browserParser->setVersionTruncation(Browser::VERSION_TRUNCATION_NONE);
        $browserParser->setUserAgent($useragent);
        $this->assertEquals($client, $browserParser->parse());
        self::$browsersTested[] = $client['short_name'];
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

    public function testAllBrowsersTested()
    {
        $allBrowsers = array_keys(Browser::getAvailableBrowsers());
        $browsersNotTested = array_diff($allBrowsers, self::$browsersTested);
        $this->assertEmpty($browsersNotTested, 'Following browsers are not tested: '.implode(', ', $browsersNotTested));
    }

    public function testGetAvailableClients()
    {
        $available = Browser::getAvailableClients();
        $this->assertGreaterThanOrEqual(count($available), count(Browser::getAvailableBrowsers()));
    }
}
