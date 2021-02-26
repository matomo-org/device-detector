<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Client;

use \Spyc;
use DeviceDetector\Parser\Client\Browser;
use PHPUnit\Framework\TestCase;

class BrowserTest extends TestCase
{
    protected static $browsersTested = [];

    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $client): void
    {
        $browserParser = new Browser();
        $browserParser->setVersionTruncation(Browser::VERSION_TRUNCATION_NONE);
        $browserParser->setUserAgent($useragent);
        $browser = $browserParser->parse();
        unset($browser['short_name']);
        $this->assertEquals($client, $browser, "UserAgent: {$useragent}");
        self::$browsersTested[] = $client['name'];
    }

    public function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/browser.yml');

        return $fixtureData;
    }

    public function testGetAvailableBrowserFamilies(): void
    {
        $this->assertGreaterThan(5, Browser::getAvailableBrowserFamilies());
    }

    public function testAllBrowsersTested(): void
    {
        $allBrowsers       = \array_values(Browser::getAvailableBrowsers());
        $browsersNotTested = \array_diff($allBrowsers, self::$browsersTested);
        $this->assertEmpty($browsersNotTested, 'This browsers are not tested: ' . \implode(', ', $browsersNotTested));
    }

    public function testGetAvailableClients(): void
    {
        $available = Browser::getAvailableClients();
        $this->assertGreaterThanOrEqual(\count($available), \count(Browser::getAvailableBrowsers()));
    }

    public function testStructureBrowsersYml(): void
    {
        $ymlDataItems = Spyc::YAMLLoad(__DIR__ . '/../../../regexes/client/browsers.yml');

        foreach ($ymlDataItems as $item) {
            $this->assertTrue(\array_key_exists('regex', $item), 'key "regex" not exist');
            $this->assertTrue(\array_key_exists('name', $item), 'key "name" not exist');
            $this->assertTrue(\array_key_exists('version', $item), 'key "version" not exist');
            $this->assertIsString($item['regex']);
            $this->assertIsString($item['name']);
            $this->assertIsString($item['version']);
        }
    }
}
