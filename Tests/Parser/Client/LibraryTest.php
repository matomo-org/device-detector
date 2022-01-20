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

use DeviceDetector\Parser\Client\Library;
use PHPUnit\Framework\TestCase;
use Spyc;

class LibraryTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $client): void
    {
        $libraryParser = new Library();
        $libraryParser->setVersionTruncation(Library::VERSION_TRUNCATION_NONE);
        $libraryParser->setUserAgent($useragent);
        $this->assertEquals($client, $libraryParser->parse());
    }

    public function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/library.yml');

        return $fixtureData;
    }

    public function testStructureLibraryYml(): void
    {
        $ymlDataItems = Spyc::YAMLLoad(__DIR__ . '/../../../regexes/client/libraries.yml');

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
