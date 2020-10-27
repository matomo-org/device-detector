<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser;

use DeviceDetector\Parser\AliasDevice;
use \Spyc;
use PHPUnit\Framework\TestCase;

/**
 * Class AliasDeviceTest
 * @package DeviceDetector\Tests\Parser
 */
class AliasDeviceTest extends TestCase
{
    static $aliasDevicesTested = array();

    /**
     * @dataProvider getFixtures
     * @param string $useragent
     * @param array $alias
     * @throws
     */
    public function testParse(string $useragent, array $alias)
    {
        $parser = new AliasDevice();
        $parser->setUserAgent($useragent);
        $this->assertEquals($alias, $parser->parse());
        self::$aliasDevicesTested[] = $alias['name'];
    }

    public function getFixtures(): array
    {
        return Spyc::YAMLLoad(realpath(__DIR__) . '/fixtures/alias_devices.yml');
    }
}
