<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
use DeviceDetector\Parser\ParserAbstract;
use Doctrine\Common\Cache\MemcacheCache;
use \Spyc;

class DeviceDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testAddClientParserInvalid()
    {
        $dd = new DeviceDetector();
        $dd->addClientParser('Invalid');
    }

    /**
     * @expectedException \Exception
     */
    public function testAddDeviceParserInvalid()
    {
        $dd = new DeviceDetector();
        $dd->addDeviceParser('Invalid');
    }

    public function testCacheSetAndGet()
    {
        if ( !extension_loaded('memcache') ) {
            $this->markTestSkipped('memcache not enabled');
        }

        $dd = new DeviceDetector();
        $memcacheServer = new \Memcache();
        $memcacheServer->connect('localhost', 11211);
        $dd->setCache(new MemcacheCache($memcacheServer));
        $this->assertInstanceOf('Doctrine\\Common\\Cache\\MemcacheCache', $dd->getCache());
    }

    public function testParseEmptyUA()
    {
        $dd = new DeviceDetector('');
        $dd->parse();
        $dd->parse(); // call second time complete code coverage
    }

    public function testParseInvalidUA()
    {
        $dd = new DeviceDetector('12345');
        $dd->parse();
    }

    /**
     * @dataProvider getFixtures
     */
    public function testParse($fixtureData)
    {
        $ua = $fixtureData['user_agent'];
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
        $uaInfo = DeviceDetector::getInfoFromUserAgent($ua);
        $this->assertEquals($fixtureData, $uaInfo);
    }

    public function getFixtures()
    {
        $fixtures = array();
        $fixtureFiles = glob(realpath(dirname(__FILE__)) . '/fixtures/*.yml');
        foreach ($fixtureFiles AS $fixturesPath) {
            $typeFixtures = \Spyc::YAMLLoad($fixturesPath);
            $deviceType = str_replace('_', ' ', substr(basename($fixturesPath), 0, -4));
            if ($deviceType != 'bots') {
                $fixtures = array_merge(array_map(function($elem) {return array($elem);}, $typeFixtures), $fixtures);
            }
        }
        return $fixtures;
    }

    /**
     * @dataProvider getVersionTruncationFixtures
     */
    public function testVersionTruncation($useragent, $truncationType, $osVersion, $clientVersion)
    {
        ParserAbstract::setVersionTruncation($truncationType);
        $dd = new DeviceDetector($useragent);
        $dd->parse();
        $this->assertEquals($osVersion, $dd->getOs('version'));
        $this->assertEquals($clientVersion, $dd->getClient('version'));
        ParserAbstract::setVersionTruncation(ParserAbstract::VERSION_TRUNCATION_NONE);
    }

    public function getVersionTruncationFixtures()
    {
        return array(
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', ParserAbstract::VERSION_TRUNCATION_NONE, '4.2.2', '34.0.1847.114'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', ParserAbstract::VERSION_TRUNCATION_BUILD, '4.2.2', '34.0.1847.114'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', ParserAbstract::VERSION_TRUNCATION_PATCH, '4.2.2', '34.0.1847'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', ParserAbstract::VERSION_TRUNCATION_MINOR, '4.2', '34.0'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', ParserAbstract::VERSION_TRUNCATION_MAJOR, '4', '34'),
        );
    }

    /**
     * @dataProvider getBotFixtures
     */
    public function testParseBots($fixtureData)
    {
        $ua = $fixtureData['user_agent'];
        $dd = new DeviceDetector($ua);
        $dd->parse();
        $this->assertTrue($dd->isBot());
        $botData = $dd->getBot();
        $this->assertEquals($botData['name'], $fixtureData['name']);
        // client and os will always be unknown for bots
        $this->assertEquals($dd->getOs('short_name'), DeviceDetector::UNKNOWN);
        $this->assertEquals($dd->getClient('short_name'), DeviceDetector::UNKNOWN);
    }

    public function getBotFixtures()
    {
        $fixturesPath = realpath(dirname(__FILE__) . '/fixtures/bots.yml');
        $fixtures = \Spyc::YAMLLoad($fixturesPath);
        return array_map(function($elem) {return array($elem);}, $fixtures);
    }

    public function testGetInforFromUABot()
    {
        $expected = array(
            'user_agent' => 'Googlebot/2.1 (http://www.googlebot.com/bot.html)',
            'bot'        => array(
                'name' => 'Googlebot',
                'category' => 'Search bot',
                'url' => 'http://www.google.com/bot.html',
                'producer' => array(
                    'name' => 'Google Inc.',
                    'url' => 'http://www.google.com'
    )
            )
        );
        $this->assertEquals($expected, DeviceDetector::getInfoFromUserAgent($expected['user_agent']));
    }

    /**
     * @dataProvider getUserAgents
     */
    public function testTypeMethods($useragent, $isBot, $isMobile, $isDesktop)
    {
        $dd = new DeviceDetector($useragent);
        $dd->discardBotInformation();
        $dd->parse();
        $this->assertEquals($isBot, $dd->isBot());
        $this->assertEquals($isMobile, $dd->isMobile());
        $this->assertEquals($isDesktop, $dd->isDesktop());
    }

    public function getUserAgents()
    {
        return array(
            array('Googlebot/2.1 (http://www.googlebot.com/bot.html)', true, false, false),
            array('Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.136 Mobile Safari/537.36', false, true, false),
            array('Mozilla/5.0 (Linux; Android 4.4.3; Build/KTU84L) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.117 Mobile Safari/537.36', false, true, false),
            array('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)', false, false, true),
            array('Mozilla/3.01 (compatible;)', false, false, false),
        );
    }

    public function testGetOs()
    {
        $dd = new DeviceDetector('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
        $this->assertNull($dd->getOs());
        $dd->parse();
        $expected = array(
            'name' => 'Windows',
            'short_name' => 'WIN',
            'version' => '7'
        );
        $this->assertEquals($expected, $dd->getOs());
    }

    public function testGetClient()
    {
        $dd = new DeviceDetector('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
        $this->assertNull($dd->getClient());
        $dd->parse();
        $expected = array(
            'type' => 'browser',
            'name' => 'Internet Explorer',
            'short_name' => 'IE',
            'version' => '9.0',
            'engine' => 'Trident'
        );
        $this->assertEquals($expected, $dd->getClient());
    }

    public function testGetBrandName()
    {
        $dd = new DeviceDetector('Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.136 Mobile Safari/537.36');
        $dd->parse();
        $this->assertEquals('Google', $dd->getBrandName());
    }
}
