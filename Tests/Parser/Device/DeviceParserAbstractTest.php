<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests\Parser\Device;

use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\Device\Mobile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DeviceParserAbstractTest extends TestCase
{
    public function testGetAvailableDeviceTypes(): void
    {
        $available = AbstractDeviceParser::getAvailableDeviceTypes();
        $this->assertGreaterThan(5, \count($available));
        $this->assertContains('desktop', \array_keys($available));
    }

    public function testGetAvailableDeviceTypeNames(): void
    {
        $available = AbstractDeviceParser::getAvailableDeviceTypeNames();
        $this->assertGreaterThan(5, \count($available));
        $this->assertContains('desktop', $available);
    }

    public function testGetFullName(): void
    {
        $this->assertEquals('', AbstractDeviceParser::getFullName('Invalid'));
        $this->assertEquals('Asus', AbstractDeviceParser::getFullName('AU'));
        $this->assertEquals('Google', AbstractDeviceParser::getFullName('GO'));
    }

    public static function getFixtures(): array
    {
        return [
            [
                'result'    => false,
                'useragent' => 'Mozilla/5.0 (Linux; Android 9; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.7204.180 Mobile Safari/537.36 Telegram-Android/12.2.10 (Zte ZTE Blade A3 2020RU; Android 9; SDK 28; LOW)',
            ],
            [
                'result'    => false,
                'useragent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.7444.171 Mobile Safari/537.36 Telegram-Android/12.2.7 (Itel itel W5006X; Android 10; SDK 29; LOW)',
            ],
            [
                'result'    => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 16; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.7444.171 Mobile Safari/537.36',
            ],
            [
                'result'    => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 14; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36',
            ],
            [
                'result'    => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 11) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/126.0.0.0 Mobile DuckDuckGo/5 Safari/537.36',
            ],
            [
                'result'    => false,
                'useragent' => 'Mozilla/5.0 (Linux; Android 15; K) Telegram-Android/12.2.10 (Tecno TECNO CL6; Android 15; SDK 35; AVERAGE)',
            ],
            [
                'result'    => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36',
            ],
            [
                'result' => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36 AlohaBrowser/5.10.4'
            ],
            [
                'result' => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.227.6834 Safari/537.36  SberBrowser/3.4.0.1123'
            ],
            [
                'result' => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 14; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.7232.2 Mobile Safari/537.36 YaApp_Android/22.116.1 YaSearchBrowser/9.20'
            ],
            [
                'result' => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like G -ecko) Chrome/142.0.0.0 Safari/537.36 EdgA/142.0.0.0',
            ],
            [
                'result' => true,
                'useragent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.118 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.33.0-gn'
            ]
        ];
    }

    /**
     * Checking the correct operation of the hasUserAgentClientHintsFragment method
     * @dataProvider getFixtures
     * @see AbstractParser::hasUserAgentClientHintsFragment
     */
    #[DataProvider('getFixtures')]
    public function testHasUserAgentClientHintsFragment(bool $result, string $useragent): void
    {
        $method = new \ReflectionMethod(Mobile::class, 'hasUserAgentClientHintsFragment');

        if (PHP_VERSION_ID < 80500) {
            $method->setAccessible(true);
        }

        $dd = new Mobile();
        $dd->setUserAgent($useragent);
        $this->assertEquals($result, $method->invoke($dd), "useragent: {$useragent}");
    }
}
