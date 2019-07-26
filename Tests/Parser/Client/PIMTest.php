<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Client;

use \Spyc;
use DeviceDetector\Parser\Client\PIM;
use PHPUnit\Framework\TestCase;

class PIMTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $client): void
    {
        $PIMParser = new PIM();
        $PIMParser->setUserAgent($useragent);
        $this->assertEquals($client, $PIMParser->parse());
    }

    public function getFixtures()
    {
        $fixtureData = Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/pim.yml');

        return $fixtureData;
    }
}
