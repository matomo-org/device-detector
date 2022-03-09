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
use PHPUnit\Framework\TestCase;

class ClientHintsTest extends TestCase
{
    public function testHeaders(): void
    {
        $headers = [
            'sec-ch-ua'                  => '"Opera";v="83", " Not;A Brand";v="99", "Chromium";v="98"',
            'sec-ch-ua-mobile'           => '?0',
            'sec-ch-ua-platform'         => 'Windows',
            'sec-ch-ua-platform-version' => '14.0.0',
        ];

        $ch = ClientHints::factory($headers);
        self::assertFalse($ch->isMobile());
        self::assertSame('Windows', $ch->getOperatingSystem());
        self::assertSame('14.0.0', $ch->getOperatingSystemVersion());
        self::assertSame([
            'Opera'        => '83',
            ' Not;A Brand' => '99',
            'Chromium'     => '98',
        ], $ch->getBrandList());
    }

    public function testHeadersHttp(): void
    {
        $headers = [
            'HTTP_SEC_CH_UA_FULL_VERSION_LIST' => '" Not A;Brand";v="99.0.0.0", "Chromium";v="98.0.4758.82", "Opera";v="98.0.4758.82"',
            'HTTP_SEC_CH_UA'                   => '" Not A;Brand";v="99", "Chromium";v="98", "Opera";v="84"',
            'HTTP_SEC_CH_UA_MOBILE'            => '?1',
            'HTTP_SEC_CH_UA_MODEL'             => 'DN2103',
            'HTTP_SEC_CH_UA_PLATFORM'          => 'Ubuntu',
            'HTTP_SEC_CH_UA_PLATFORM_VERSION'  => '3.7',
            'HTTP_SEC_CH_UA_FULL_VERSION'      => '98.0.14335.105',
        ];

        $ch = ClientHints::factory($headers);
        self::assertTrue($ch->isMobile());
        self::assertSame('Ubuntu', $ch->getOperatingSystem());
        self::assertSame('3.7', $ch->getOperatingSystemVersion());
        self::assertSame([
            ' Not A;Brand' => '99.0.0.0',
            'Chromium'     => '98.0.4758.82',
            'Opera'        => '98.0.4758.82',
        ], $ch->getBrandList());
        self::assertSame('DN2103', $ch->getModel());
    }

    public function testHeadersJavascript(): void
    {
        $headers = [
            'fullVersionList' => [
                ['brand' => ' Not A;Brand', 'version' => '99.0.0.0'],
                ['brand' => 'Chromium', 'version' => '99.0.4844.51'],
                ['brand' => 'Google Chrome', 'version' => '99.0.4844.51'],
            ],
            'mobile'          => false,
            'model'           => '',
            'platform'        => 'Windows',
            'platformVersion' => '10.0.0',
        ];

        $ch = ClientHints::factory($headers);
        self::assertFalse($ch->isMobile());
        self::assertSame('Windows', $ch->getOperatingSystem());
        self::assertSame('10.0.0', $ch->getOperatingSystemVersion());
        self::assertSame([
            ' Not A;Brand'  => '99.0.0.0',
            'Chromium'      => '99.0.4844.51',
            'Google Chrome' => '99.0.4844.51',
        ], $ch->getBrandList());
        self::assertSame('', $ch->getModel());
    }
}
