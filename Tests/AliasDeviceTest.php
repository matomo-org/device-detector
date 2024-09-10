<?php


declare(strict_types=1);

namespace DeviceDetector\Tests;

use DeviceDetector\Parser\Device\AliasDevice;
use DeviceDetector\Parser\IndexerClient;
use PHPUnit\Framework\TestCase;

class AliasDeviceTest extends TestCase
{

    /**
     * @dataProvider getFixtures
     * @return void
     */
    public function testParse(array $fixtureData): void
    {
        $useragent = $fixtureData['user_agent'];
        $alias     = $fixtureData['alias'];

        $deviceAlias = new AliasDevice();
        $deviceAlias->setUserAgent($useragent);
        $result = $deviceAlias->parse();

        $this->assertEquals($result, $alias);
    }

    public function getFixtures(): array
    {
        $fixtures     = [];
        $fixtureFiles = \glob(\realpath(__DIR__) . '/fixtures/alias_devices*.yml');

        foreach ($fixtureFiles as $fixturesPath) {
            $typeFixtures = \Spyc::YAMLLoad($fixturesPath);

            $fixtures = \array_merge(\array_map(static function ($elem) {
                return [$elem];
            }, $typeFixtures), $fixtures);
        }

        return $fixtures;
    }
}