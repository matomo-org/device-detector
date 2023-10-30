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

use DeviceDetector\Parser\Client\FeedReader;
use PHPUnit\Framework\TestCase;
use Spyc;

class FeedReaderTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $client): void
    {
        $feedReaderParser = new FeedReader();
        $feedReaderParser->setVersionTruncation(FeedReader::VERSION_TRUNCATION_NONE);
        $feedReaderParser->setUserAgent($useragent);
        $this->assertEquals($client, $feedReaderParser->parse(), "UserAgent: {$useragent}");
    }

    public function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/feed_reader.yml');

        return $fixtureData;
    }

    public function testStructureFeedReaderYml(): void
    {
        $ymlDataItems = Spyc::YAMLLoad(__DIR__ . '/../../../regexes/client/feed_readers.yml');

        foreach ($ymlDataItems as $item) {
            $this->assertTrue(\array_key_exists('regex', $item), 'key "regex" not exist');
            $this->assertTrue(\array_key_exists('name', $item), 'key "name" not exist');
            $this->assertTrue(\array_key_exists('version', $item), 'key "version" not exist');
            $this->assertTrue(\array_key_exists('url', $item), 'key "url" not exist');
            $this->assertIsString($item['regex']);
            $this->assertIsString($item['name']);
            $this->assertIsString($item['version']);
            $this->assertIsString($item['url']);
        }
    }
}
