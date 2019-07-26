<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Client;

use DeviceDetector\Parser\Client\Browser;
use \Spyc;
use PHPUnit\Framework\TestCase;

class BrowserTest extends TestCase
{
    static $browsersTested = [];

    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $client): void
    {
        $browserParser = new Browser();
        $browserParser->setVersionTruncation(Browser::VERSION_TRUNCATION_NONE);
        $browserParser->setUserAgent($useragent);
        $this->assertEquals($client, $browserParser->parse());
        self::$browsersTested[] = $client['short_name'];
    }

    public function getFixtures()
    {
        $fixtureData = Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/browser.yml');

        return $fixtureData;
    }

    public function testGetAvailableBrowserFamilies(): void
    {
        $this->assertGreaterThan(5, Browser::getAvailableBrowserFamilies());
    }

    public function testAllBrowsersTested(): void
    {
        $allBrowsers       = array_keys(Browser::getAvailableBrowsers());
        $browsersNotTested = array_diff($allBrowsers, self::$browsersTested);
        $this->assertEmpty($browsersNotTested, 'Following browsers are not tested: ' . implode(', ', $browsersNotTested));
    }

    public function testGetAvailableClients(): void
    {
        $available = Browser::getAvailableClients();
        $this->assertGreaterThan(count($available), count(Browser::getAvailableBrowsers()));
    }
}
