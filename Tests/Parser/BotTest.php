<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests\Parser;

use DeviceDetector\Parser\Bot;
use PHPUnit\Framework\TestCase;

class BotTest extends TestCase
{
    public function testGetInfoFromUABot(): void
    {
        $expected  = [
            'name'     => 'Googlebot',
            'category' => 'Search bot',
            'url'      => 'https://developers.google.com/search/docs/crawling-indexing/overview-google-crawlers',
            'producer' => [
                'name' => 'Google Inc.',
                'url'  => 'https://www.google.com/',
            ],
        ];
        $botParser = new Bot();
        $botParser->setUserAgent('Googlebot/2.1 (http://www.googlebot.com/bot.html)');
        $this->assertEquals($expected, $botParser->parse());
    }

    public function testParseNoDetails(): void
    {
        $botParser = new Bot();
        $botParser->discardDetails();
        $botParser->setUserAgent('Googlebot/2.1 (http://www.googlebot.com/bot.html)');
        $this->assertEquals([true], $botParser->parse());
    }

    public function testParseNoBot(): void
    {
        $botParser = new Bot();
        $botParser->setUserAgent('Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; SV1; SE 2.x)');
        $this->assertNull($botParser->parse());
    }
}
