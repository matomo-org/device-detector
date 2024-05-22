<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Client;

use DeviceDetector\Parser\Client\Library;
use PHPUnit\Framework\TestCase;
use Spyc;

class LibraryTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, array $client)
    {
        $libraryParser = new Library();
        $libraryParser->setVersionTruncation(Library::VERSION_TRUNCATION_NONE);
        $libraryParser->setUserAgent($useragent);
        $this->assertEquals($client, $libraryParser->parse());
    }

    public function getFixtures()
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/library.yml');

        return $fixtureData;
    }

    public function testStructureLibraryYml()
    {
        $ymlDataItems = Spyc::YAMLLoad(__DIR__ . '/../../../regexes/client/libraries.yml');

        foreach ($ymlDataItems as $item) {
            $this->assertTrue(\array_key_exists('regex', $item), 'key "regex" not exist');
            $this->assertTrue(\array_key_exists('name', $item), 'key "name" not exist');
            $this->assertTrue(\array_key_exists('version', $item), 'key "version" not exist');
            $this->assertNotNull($item['regex']);
            $this->assertNotNull($item['name']);
            $this->assertNotNull($item['version']);
        }
    }
}
