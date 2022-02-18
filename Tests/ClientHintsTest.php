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
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.3',
                'headers'    => [
                    'Sec-CH-UA'                  => 'Not;A Brand";v="99", "Google Chrome";v="97", "Chromium";v="97"',
                    'Sec-CH-UA-Mobile'           => '?0',
                    'Sec-CH-UA-Platform'         => 'Windows',
                    'Sec-CH-UA-Platform-Version' => '14.0.0',
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
            ], [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.3',
                'headers'    => [
                    'Sec-CH-UA'          => 'Not;A Brand";v="99", "Google Chrome";v="97", "Chromium";v="97"',
                    'Sec-CH-UA-Mobile'   => '?0',
                    'Sec-CH-UA-Platform' => 'Windows',
                ],
                'fixture'    => [
                    'os' => [
                        'name'       => 'Windows',
                        'short_name' => 'WIN',
                        'version'    => '10',
                        'platform'   => 'x64',
                        'family'     => 'Windows',
                    ],
                ],
            ], [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.3',
                'headers'    => [],
                'fixture'    => [
                    'os' => [
                        'name'       => 'Windows',
                        'short_name' => 'WIN',
                        'version'    => '10',
                        'platform'   => 'x64',
                        'family'     => 'Windows',
                    ],
                ],
            ], [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36 Edg/95.0.1020.44',
                'headers'    => [
                    'sec-ch-ua'                  => '" Not A;Brand";v="99", "Chromium";v="95", "Microsoft Edge";v="95"',
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
            ], [
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
