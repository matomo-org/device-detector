<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests;

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use PHPUnit\Framework\TestCase;

class ClientHintsTest extends TestCase
{
    public function getOsFixtures(): array
    {
        return [
            [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36 OPR/83.0.4254.27',
                'headers'    => [
                    'sec-ch-ua'                  => '"Opera";v="83", " Not;A Brand";v="99", "Chromium";v="98"',
                    'sec-ch-ua-mobile'           => '?0',
                    'sec-ch-ua-platform'         => 'Windows',
                    'sec-ch-ua-platform-version' => '14.0.0',
                ],
                'fixture'    => [
                    'os' => [
                        'name'       => 'Windows',
                        'short_name' => 'WIN',
                        'version'    => '11',
                        'platform'   => 'x64',
                        'family'     => 'Windows',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOsFixtures
     */
    public function testOs(string $useragent, array $headers, array $fixture): void
    {
        AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);
        $ch = ClientHints::factory($headers);
        $dd = new DeviceDetector($useragent, $ch);
        $dd->parse();
        $this->assertEquals($dd->getOs(), $fixture['os']);
    }
}
