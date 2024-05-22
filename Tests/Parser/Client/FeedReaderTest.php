<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Client;

use DeviceDetector\Parser\Client\FeedReader;
use PHPUnit\Framework\TestCase;
use Spyc;

class FeedReaderTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, array $client)
    {
        $feedReaderParser = new FeedReader();
        $feedReaderParser->setVersionTruncation(FeedReader::VERSION_TRUNCATION_NONE);
        $feedReaderParser->setUserAgent($useragent);
        $this->assertEquals($client, $feedReaderParser->parse(), "UserAgent: {$useragent}");
    }

    public function getFixtures()
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/feed_reader.yml');

        return $fixtureData;
    }

    public function testStructureFeedReaderYml()
    {
        $ymlDataItems = Spyc::YAMLLoad(__DIR__ . '/../../../regexes/client/feed_readers.yml');

        foreach ($ymlDataItems as $item) {
            $this->assertTrue(\array_key_exists('regex', $item), 'key "regex" not exist');
            $this->assertTrue(\array_key_exists('name', $item), 'key "name" not exist');
            $this->assertTrue(\array_key_exists('version', $item), 'key "version" not exist');
            $this->assertTrue(\array_key_exists('url', $item), 'key "url" not exist');
            $this->assertNotNull($item['regex']);
            $this->assertNotNull($item['name']);
            $this->assertNotNull($item['version']);
            $this->assertNotNull($item['url']);
        }
    }
}
