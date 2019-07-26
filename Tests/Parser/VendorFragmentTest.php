<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser;

use \Spyc;
use DeviceDetector\Parser\VendorFragment;
use PHPUnit\Framework\TestCase;

class VendorFragmentTest extends TestCase
{
    static $regexesTested = [];

    /**
     * @dataProvider getFixtures
     */
    public function testParse($useragent, $vendor): void
    {
        $vfParser = new VendorFragment();
        $vfParser->setUserAgent($useragent);
        $this->assertEquals(['brand' => $vendor], $vfParser->parse());
        self::$regexesTested[] = $vfParser->getMatchedRegex();
    }

    public function getFixtures()
    {
        $fixtureData = Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/fixtures/vendorfragments.yml');

        return $fixtureData;
    }

    public function testAllRegexesTested(): void
    {
        $regexesNotTested = [];

        $vendorRegexes = Spyc::YAMLLoad(realpath(__DIR__ . '/../../regexes/') . DIRECTORY_SEPARATOR . 'vendorfragments.yml');

        foreach ($vendorRegexes as $vendor => $regexes) {
            foreach ($regexes as $regex) {
                if (in_array($regex, self::$regexesTested)) {
                    continue;
                }

                $regexesNotTested[] = $vendor . ' / ' . $regex;
            }
        }

        $this->assertEmpty($regexesNotTested, 'Following vendor fragments are not tested: ' . implode(', ', $regexesNotTested));
    }
}
