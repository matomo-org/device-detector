<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use PHPUnit\Framework\TestCase;

class ClientHintsTest extends TestCase
{
    public function getOsFixtures()
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
                    'name'       => 'Windows',
                    'short_name' => 'WIN',
                    'version'    => '11',
                    'platform'   => 'x64',
                    'family'     => 'Windows',
                ],
            ], [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.3',
                'headers'    => [
                    'Sec-CH-UA'          => 'Not;A Brand";v="99", "Google Chrome";v="97", "Chromium";v="97"',
                    'Sec-CH-UA-Mobile'   => '?0',
                    'Sec-CH-UA-Platform' => 'Windows',
                ],
                'fixture'    => [
                    'name'       => 'Windows',
                    'short_name' => 'WIN',
                    'version'    => '10',
                    'platform'   => 'x64',
                    'family'     => 'Windows',
                ],
            ], [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.3',
                'headers'    => [],
                'fixture'    => [
                    'name'       => 'Windows',
                    'short_name' => 'WIN',
                    'version'    => '10',
                    'platform'   => 'x64',
                    'family'     => 'Windows',
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
        $this->assertEquals($dd->getOs(), $fixture);
    }
}
