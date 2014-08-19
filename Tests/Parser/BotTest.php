<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser;

use DeviceDetector\Parser\Bot;
use \Spyc;

class BotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $bot)
    {
        $botParser = new Bot();
        $botParser->setUserAgent($useragent);
        $this->assertEquals($bot, $botParser->parse());
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/bots.yml');
        return $fixtureData;
    }

    public function testParseNoDetails()
    {
        $fixtures = $this->getFixtures();
        $fixture  = array_shift($fixtures);
        $botParser = new Bot();
        $botParser->discardDetails();
        $botParser->setUserAgent($fixture['user_agent']);
        $this->assertTrue($botParser->parse());
    }

    public function testParseNoBot()
    {
        $botParser = new Bot();
        $botParser->setUserAgent('Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; SV1; SE 2.x)');
        $this->assertNull($botParser->parse());
    }
}
