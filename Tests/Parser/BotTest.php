<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
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
        $borParser = new Bot();
        $borParser->setUserAgent($useragent);
        $this->assertEquals($bot, $borParser->parse());
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/bots.yml');
        return $fixtureData;
    }
}
