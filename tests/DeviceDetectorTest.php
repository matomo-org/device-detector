<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

require __DIR__ . '/../vendor/autoload.php';

class UserAgentParserEnhancedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group Plugins
     */
    public function testParse()
    {
        $fixturesPath = realpath(dirname(__FILE__) . '/userAgentParserEnhancedFixtures.yml');
        $fixtures = Spyc::YAMLLoad($fixturesPath);
        foreach ($fixtures as $fixtureData) {
            $ua = $fixtureData['user_agent'];
            $uaInfo = UserAgentParserEnhanced::getInfoFromUserAgent($ua);
            $parsed[] = $uaInfo;
        }
        if($fixtures != $parsed) {
            $processed = Spyc::YAMLDump($parsed, false, $wordWrap = 0);
            $processedPath = $fixturesPath . '.new';
            file_put_contents($processedPath, $processed);
            $diffCommand = "diff -a1 -b1";
            $command = "{$diffCommand} $fixturesPath $processedPath";
            echo $command . "\n";
            echo shell_exec($command);

            echo "\nThe processed data was stored in: $processedPath ".
                "\n $ cp $processedPath $fixturesPath ".
                "\n to copy the file over if it is valid.";

            $this->assertTrue(false);

        }
        $this->assertTrue(true);
    }

    /**
     * @group Plugins
     * @dataProvider getAllOs
     */
    public function testOSInGroup($os)
    {
        $familyOs = call_user_func_array('array_merge', UserAgentParserEnhanced::$osFamilies);
        $this->assertContains($os, $familyOs);
    }

    public function getAllOs()
    {
        $allOs = array_values(UserAgentParserEnhanced::$osShorts);
        $allOs = array_map(function($os){ return array($os); }, $allOs);
        return $allOs;
    }
}
