<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace DeviceDetector\Tests;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
use \Spyc;

class DeviceDetectorTest extends \PHPUnit_Framework_TestCase
{

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
    public function versionTruncationTest($useragent, $truncationType, $osVersion, $clientVersion)
    {
        DeviceParserAbstract::setVersionTruncation($truncationType);
        $dd = new DeviceDetector($useragent);
        $this->assertEquals($osVersion, $dd->getOs('version'));
        $this->assertEquals($clientVersion, $dd->getClient('version'));
    }

    public function getVersionTruncationFixtures()
    {
        return array(
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', DeviceParserAbstract::VERSION_TRUNCATION_NONE, '4.2.2', '34.0.1847.114'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', DeviceParserAbstract::VERSION_TRUNCATION_BUILD, '4.2.2', '34.0.1847.114'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', DeviceParserAbstract::VERSION_TRUNCATION_PATCH, '4.2.2', '34.0.1847'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', DeviceParserAbstract::VERSION_TRUNCATION_MINOR, '4.2', '34.0'),
            array('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', DeviceParserAbstract::VERSION_TRUNCATION_MAJOR, '4', '34'),
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
}
