<?php declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Tests\Parser\Client;

use \Spyc;
use DeviceDetector\Parser\Client\Library;
use PHPUnit\Framework\TestCase;

class LibraryTest extends TestCase
{
    /**
     * @dataProvider getFixtures
     */
    public function testParse(string $useragent, array $client): void
    {
        $libraryParser = new Library();
        $libraryParser->setUserAgent($useragent);
        $this->assertEquals($client, $libraryParser->parse());
    }

    public function getFixtures(): array
    {
        $fixtureData = Spyc::YAMLLoad(\realpath(__DIR__) . '/fixtures/library.yml');

        return $fixtureData;
    }
}
