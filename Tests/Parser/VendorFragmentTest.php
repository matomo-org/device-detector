<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Tests\Parser;

use DeviceDetector\Parser\VendorFragment;
use \Spyc;
use PHPUnit\Framework\TestCase;

class VendorFragmentTest extends TestCase
{
    static $regexesTested = array();

    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $vendor)
    {
        $vfParser = new VendorFragment();
        $vfParser->setUserAgent($useragent);
        $this->assertEquals($vendor, $vfParser->parse());
        self::$regexesTested[] = $vfParser->getMatchedRegex();
    }

    public function getFixtures()
    {
        $fixtureData = \Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/vendorfragments.yml');
        return $fixtureData;
    }

    public function testAllRegexesTested()
    {
        $regexesNotTested = array();

        $vendorRegexes = Spyc::YAMLLoad(realpath(__DIR__ . '/../../regexes/') . DIRECTORY_SEPARATOR . 'vendorfragments.yml');

        foreach ($vendorRegexes as $vendor => $regexes) {
            foreach ($regexes as $regex) {
                if (!in_array($regex, self::$regexesTested)) {
                    $regexesNotTested[] = $vendor . ' / ' . $regex;
                }
            }

        }

        $this->assertEmpty($regexesNotTested, 'Following vendor fragments are not tested: ' . implode(', ', $regexesNotTested));
    }
}
