<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser;

use \Spyc;
use DeviceDetector\Parser\AliasDevice;
use PHPUnit\Framework\TestCase;

/**
 * Class AliasDeviceTest
 * @package DeviceDetector\Tests\Parser
 */
class AliasDeviceTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     * @param string $useragent
     * @param array $alias
     * @throws
     */
    public function testParse(string $useragent, array $alias): void
    {
        $parser = new AliasDevice();
        $parser->setUserAgent($useragent);
        $result = $parser->parse();
        if (is_array($result)){
            $this->assertArrayHasKey('name', $result);
            $this->assertEquals($alias, $result);
            return;
        }
        $this->assertEquals(null, $result);

    }

    public function getFixtures(): array
    {
        return Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/alias_devices.yml');
    }
}
