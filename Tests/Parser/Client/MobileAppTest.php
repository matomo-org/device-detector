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
use DeviceDetector\Parser\Client\MobileApp;
use PHPUnit\Framework\TestCase;

class MobileAppTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $client): void
    {
        $mobileAppParser = new MobileApp();
        $mobileAppParser->setUserAgent($useragent);
        $this->assertEquals($client, $mobileAppParser->parse());
    }

    public function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/mobile_app.yml');

        return $fixtureData;
    }
}
