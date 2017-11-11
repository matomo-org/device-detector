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
use PHPUnit\Framework\TestCase;

class BotTest extends TestCase
{
    public function testGetInfoFromUABot()
    {
        $expected = array(
            'name'     => 'Googlebot',
            'category' => 'Search bot',
            'url'      => 'http://www.google.com/bot.html',
            'producer' => array(
                'name' => 'Google Inc.',
                'url'  => 'http://www.google.com'
            )
        );
        $botParser = new Bot();
        $botParser->setUserAgent('Googlebot/2.1 (http://www.googlebot.com/bot.html)');
        $this->assertEquals($expected, $botParser->parse());
    }

    public function testParseNoDetails()
    {
        $botParser = new Bot();
        $botParser->discardDetails();
        $botParser->setUserAgent('Googlebot/2.1 (http://www.googlebot.com/bot.html)');
        $this->assertTrue($botParser->parse());
    }

    public function testParseNoBot()
    {
        $botParser = new Bot();
        $botParser->setUserAgent('Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; SV1; SE 2.x)');
        $this->assertNull($botParser->parse());
    }
}
