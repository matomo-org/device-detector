<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Client;

use DeviceDetector\Parser\Client\PIM;
use PHPUnit\Framework\TestCase;
use Spyc;

class PIMTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, array $client)
    {
        $PIMParser = new PIM();
        $PIMParser->setVersionTruncation(PIM::VERSION_TRUNCATION_NONE);
        $PIMParser->setUserAgent($useragent);
        $this->assertEquals($client, $PIMParser->parse());
    }

    public function getFixtures()
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/pim.yml');

        return $fixtureData;
    }

    public function testStructurePimYml()
    {
        $ymlDataItems = Spyc::YAMLLoad(__DIR__ . '/../../../regexes/client/pim.yml');

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
