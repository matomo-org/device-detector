<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests\Parser\Client;

use DeviceDetector\ClientHints;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Client\Browser\Engine;
use DeviceDetector\Parser\Client\Hints\BrowserHints;
use PHPUnit\Framework\TestCase;
use Spyc;

class BrowserTest extends TestCase
{
    protected static $browsersTested = [];

    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $client, ?array $headers = null): void
    {
        $browserParser = new Browser();
        $browserParser->setVersionTruncation(Browser::VERSION_TRUNCATION_NONE);
        $browserParser->setUserAgent($useragent);

        if (null !== $headers) {
            $browserParser->setClientHints(ClientHints::factory($headers));
        }

        $browser = $browserParser->parse();
        unset($browser['short_name']);

        $this->assertEquals($client, $browser, "UserAgent: {$useragent}");
        $this->assertTrue($this->checkBrowserEngine($browser['engine']), \sprintf(
            "UserAgent: %s\nEngine wrong name: `%s`",
            $useragent,
            $browser['engine']
        ));

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

    public function testBrowserFamiliesNoDuplicates(): void
    {
        $browsers = Browser::getAvailableBrowserFamilies();

        foreach ($browsers as $browser => $families) {
            $shortcodes = \array_count_values($families);

            foreach ($shortcodes as $shortcode => $count) {
                $this->assertEquals(
                    $count,
                    1,
                    "Family {$browser}: contains duplicate of shortcode {$shortcode}"
                );
            }
        }
    }

    /**
     * @return array
     */
    public function getFixturesBrowserHints(): array
    {
        $method = new \ReflectionMethod(BrowserHints::class, 'getRegexes');
        $method->setAccessible(true);
        $hints    = $method->invoke(new BrowserHints());
        $fixtures = [];

        foreach ($hints as $hash => $name) {
            $fixtures[] = \compact('name');
        }

        return $fixtures;
    }

    /**
     * @dataProvider getFixturesBrowserHints
     */
    public function testBrowserHintsForAvailableBrowsers(string $name): void
    {
        $browserShort = Browser::getBrowserShortName($name);
        $this->assertNotEquals(
            null,
            $browserShort,
            \sprintf('Browser name "%s" was not found in $availableBrowsers.', $name)
        );
    }

    protected function checkBrowserEngine(string $engine): bool
    {
        if ('' === $engine) {
            return true;
        }

        $engines         = Engine::getAvailableEngines();
        $enginePos       = \array_search($engine, $engines, false);
        $engineReference = $engines[$enginePos] ?? null;

        return $engineReference === $engine;
    }
}
