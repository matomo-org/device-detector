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
use DeviceDetector\Yaml\Symfony;
use PHPUnit\Framework\TestCase;

class DeviceDetectorTest extends TestCase
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

    public function testDevicesYmlFiles()
    {
        $fixtureFiles = glob(realpath(dirname(__FILE__)) . '/../regexes/device/*.yml');
        foreach ($fixtureFiles AS $file) {
            $ymlData = \Spyc::YAMLLoad($file);
            foreach ($ymlData AS $brand => $regex) {
                $this->assertArrayHasKey('regex', $regex);
                $this->assertTrue(strpos($regex['regex'], '||') === false, sprintf(
                    "Detect `||` in regex, file %s, brand %s, common regex %s",
                    $file,
                    $brand,
                    $regex['regex']
                ));
                if (array_key_exists('models', $regex)) {
                    $this->assertInternalType('array', $regex['models']);
                    foreach ($regex['models'] AS $model) {
                        $this->assertArrayHasKey('regex', $model);
                        $this->assertArrayHasKey('model', $model, sprintf(
                            "Key model not exist, file %s, brand %s, model regex %s",
                            $file,
                            $brand,
                            $model['regex']
                        ));
                        $this->assertTrue(strpos($model['regex'], '||') === false, sprintf(
                            "Detect `||` in regex, file %s, brand %s, model regex %s",
                            $file,
                            $brand,
                            $model['regex']
                        ));
                    }
                } else {
                    $this->assertArrayHasKey('device', $regex);
                    $this->assertArrayHasKey('model', $regex);
                    $this->assertInternalType('string', $regex['model']);
                }
            }
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testSetYamlParserInvalid()
    {
        $dd = new DeviceDetector();
        $dd->setYamlParser('Invalid');
    }

    public function testSetYamlParser()
    {
        $dd = new DeviceDetector();
        $dd->setYamlParser(new Symfony());
        $dd->setUserAgent('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36');
        $dd->parse();
    }
    /**
     * @expectedException \Exception
     */
    public function testSetCacheInvalid()
    {
        $dd = new DeviceDetector();
        $dd->setCache('Invalid');
    }

    public function testCacheSetAndGet()
    {
        if ( !extension_loaded('memcache') || !class_exists('\Doctrine\Common\Cache\MemcacheCache') ) {
            $this->markTestSkipped('memcache not enabled');
        }

        $dd = new DeviceDetector();
        $memcacheServer = new \Memcache();
        $memcacheServer->connect('localhost', 11211);
        $dd->setCache(new \Doctrine\Common\Cache\MemcacheCache($memcacheServer));
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

    public function testIsParsed()
    {
        $dd = new DeviceDetector('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36');
        $this->assertFalse($dd->isParsed());
        $dd->parse();
        $this->assertTrue($dd->isParsed());
    }

    /**
     * @dataProvider getFixtures
     */
    public function testParse($fixtureData)
    {
        $ua = $fixtureData['user_agent'];
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
        $uaInfo = DeviceDetector::getInfoFromUserAgent($ua);
        $this->assertEquals($fixtureData, $uaInfo, "UserAgent: {$ua}");
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

    public function testInstanceReusage()
    {
        $userAgents = array(
            'Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36' => array(
                'device' => array(
                    'brand' => 'Archos',
                    'model' => '101 PLATINUM'
                )
            ),
            'Opera/9.80 (Linux mips; U; HbbTV/1.1.1 (; Vestel; MB95; 1.0; 1.0; ); en) Presto/2.10.287 Version/12.00' => array(
                'device' => array(
                    'brand' => 'Vestel',
                    'model' => 'MB95'
                )
            ),
            'Sraf/3.0 (Linux i686 ; U; HbbTV/1.1.1 (+PVR+DL;NEXUS; TV44; sw1.0) CE-HTML/1.0 Config(L:eng,CC:DEU); en/de)' => array(
                'device' => array(
                    'brand' => '',
                    'model' => '',
                )
            )
        );

        $deviceDetector = new DeviceDetector();

        foreach ($userAgents as $userAgent => $expected) {
            $deviceDetector->setUserAgent($userAgent);
            $deviceDetector->parse();
            $this->assertEquals($expected['device']['brand'], $deviceDetector->getBrandName());
            $this->assertEquals($expected['device']['model'], $deviceDetector->getModel());
        }

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
        $this->assertEquals($botData, $fixtureData['bot']);
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

    public function testGetInfoFromUABot()
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


    public function testParseNoDetails()
    {
        $user_agent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
        $dd = new DeviceDetector($user_agent);
        $dd->discardBotInformation();
        $dd->parse();
        $this->assertTrue($dd->getBot());
    }

    public function testMagicMMethods()
    {
        $ua = 'Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.136 Mobile Safari/537.36';
        $dd = new DeviceDetector($ua);
        $dd->parse();
        $this->assertTrue($dd->isSmartphone());
        $this->assertFalse($dd->isFeaturePhone());
        $this->assertFalse($dd->isTablet());
        $this->assertFalse($dd->isPhablet());
        $this->assertFalse($dd->isCarBrowser());
        $this->assertFalse($dd->isSmartDisplay());
        $this->assertFalse($dd->isTV());
        $this->assertFalse($dd->isConsole());
        $this->assertFalse($dd->isPortableMediaPlayer());
        $this->assertFalse($dd->isCamera());

        $this->assertTrue($dd->isBrowser());
        $this->assertFalse($dd->isLibrary());
        $this->assertFalse($dd->isMediaPlayer());
        $this->assertFalse($dd->isMobileApp());
        $this->assertFalse($dd->isPIM());
        $this->assertFalse($dd->isFeedReader());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testInvalidMagicMethod()
    {
        $dd = new DeviceDetector('Mozilla/5.0');
        $dd->parse();
        $dd->inValidMethod();
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
            // Mobile only browsers:
            array('Opera/9.80 (J2ME/MIDP; Opera Mini/9.5/37.8069; U; en) Presto/2.12.423 Version/12.16', false, true, false),
            array('Mozilla/5.0 (X11; U; Linux i686; th-TH@calendar=gregorian) AppleWebKit/534.12 (KHTML, like Gecko) Puffin/1.3.2665MS Safari/534.12', false, true, false),
            array('Mozilla/5.0 (Linux; Android 4.4.4; MX4 Pro Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/33.0.0.0 Mobile Safari/537.36; 360 Aphone Browser (6.9.7)', false, true, false),
            array('Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_7; xx) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Safari/530.17 Skyfire/6DE', false, true, false),
            // useragent containing non unicode chars
            array('Mozilla/5.0 (Linux; U; Android 4.1.2; ru-ru; PMP7380D3G Build/JZO54K) AppleWebKit/534.30 (KHTML, ÃÂºÃÂ°ÃÂº Gecko) Version/4.0 Safari/534.30', false, true, false),
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
            'version' => '7',
            'platform' => 'x64'
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
            'engine' => 'Trident',
            'engine_version' => '5.0',
        );
        $this->assertEquals($expected, $dd->getClient());
    }

    public function testGetBrandName()
    {
        $dd = new DeviceDetector('Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.136 Mobile Safari/537.36');
        $dd->parse();
        $this->assertEquals('Google', $dd->getBrandName());
    }

    public function testIsTouchEnabled()
    {
        $dd = new DeviceDetector('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; ARM; Trident/6.0; Touch; ARMBJS)');
        $dd->parse();
        $this->assertTrue($dd->isTouchEnabled());
    }


    public function testSkipBotDetection()
    {
        $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        $dd = new DeviceDetector($ua);
        $dd->parse();
        $this->assertFalse($dd->isMobile());
        $this->assertTrue($dd->isBot());
        $dd = new DeviceDetector($ua);
        $dd->skipBotDetection();
        $dd->parse();
        $this->assertTrue($dd->isMobile());
        $this->assertFalse($dd->isBot());
    }
}
