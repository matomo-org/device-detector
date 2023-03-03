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

use Closure;
use DeviceDetector\Cache\DoctrineBridge;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Client\MobileApp;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\Device\Mobile;
use DeviceDetector\Yaml\Symfony;
use Doctrine\Common\Cache\MemcachedCache;
use PHPUnit\Framework\TestCase;

class DeviceDetectorTest extends TestCase
{
    public function testAddClientParserInvalid(): void
    {
        $this->expectException(\Throwable::class);
        $dd = new DeviceDetector();
        $dd->addClientParser('Invalid');
    }

    public function testAddDeviceParserInvalid(): void
    {
        $this->expectException(\Throwable::class);
        $dd = new DeviceDetector();
        $dd->addDeviceParser('Invalid');
    }

    public function testDevicesYmlFiles(): void
    {
        $allowedKeys  = ['regex', 'device', 'models', 'model', 'brand'];
        $fixtureFiles = \glob(\realpath(__DIR__) . '/../regexes/device/*.yml');

        foreach ($fixtureFiles as $file) {
            $ymlData = \Spyc::YAMLLoad($file);

            $availableDeviceTypeNames = AbstractDeviceParser::getAvailableDeviceTypeNames();

            foreach ($ymlData as $brand => $regex) {
                $this->assertArrayHasKey('regex', $regex);

                $keys = \array_keys($regex);

                foreach ($keys as $key) {
                    $this->assertTrue(\in_array($key, $allowedKeys), \sprintf(
                        'Unknown key `%s`, file %s, brand %s',
                        $key,
                        $file,
                        $brand
                    ));
                }

                $this->assertTrue(false === \strpos($regex['regex'], '||'), \sprintf(
                    'Detect `||` in regex, file %s, brand %s, common regex %s',
                    $file,
                    $brand,
                    $regex['regex']
                ));

                $this->assertTrue($this->checkRegexVerticalLineClosingGroup($regex['regex']), \sprintf(
                    'Detect `|)` in regex, file %s, brand %s, common regex %s',
                    $file,
                    $brand,
                    $regex['regex']
                ));

                $this->assertTrue($this->checkRegexRestrictionEndCondition($regex['regex']), \sprintf(
                    'Detect end of regular expression does not match the format `(?:[);/ ]|$)`, file %s, brand %s, common regex %s',
                    $file,
                    $brand,
                    $regex['regex']
                ));

                if (\array_key_exists('device', $regex)) {
                    $this->assertTrue(\in_array($regex['device'], $availableDeviceTypeNames), \sprintf(
                        "Unknown device type `%s`, file %s, brand %s, common regex %s\n\nAvailable types:\n%s\n",
                        $regex['device'],
                        $file,
                        $brand,
                        $regex['regex'],
                        \implode(PHP_EOL, $availableDeviceTypeNames)
                    ));
                }

                if (\array_key_exists('models', $regex)) {
                    $this->assertIsArray($regex['models']);

                    foreach ($regex['models'] as $model) {
                        $keys = \array_keys($model);

                        foreach ($keys as $key) {
                            $this->assertTrue(\in_array($key, $allowedKeys), \sprintf(
                                'Unknown key `%s`, file %s, brand %s, model regex %s',
                                $key,
                                $file,
                                $brand,
                                $model['regex']
                            ));
                        }

                        $this->assertArrayHasKey('regex', $model);
                        $this->assertArrayHasKey('model', $model, \sprintf(
                            'Key model not exist, file %s, brand %s, model regex %s',
                            $file,
                            $brand,
                            $model['regex']
                        ));
                        $this->assertTrue(false === \strpos($model['regex'], '||'), \sprintf(
                            'Detect `||` in regex, file %s, brand %s, model regex %s',
                            $file,
                            $brand,
                            $model['regex']
                        ));

                        $this->assertTrue($this->checkRegexVerticalLineClosingGroup($model['regex']), \sprintf(
                            'Detect `|)` in regex, file %s, brand %s, model regex %s',
                            $file,
                            $brand,
                            $model['regex']
                        ));

                        $this->assertTrue($this->checkRegexRestrictionEndCondition($model['regex']), \sprintf(
                            'Detect end of regular expression does not match the format `(?:[);/ ]|$)`, file %s, brand %s, model regex %s',
                            $file,
                            $brand,
                            $model['regex']
                        ));

                        if (!\array_key_exists('device', $model)) {
                            continue;
                        }

                        $this->assertTrue(\in_array($model['device'], $availableDeviceTypeNames), \sprintf(
                            "Unknown device type `%s`, file %s, brand %s, model regex %s\n\nAvailable types:\n%s\n",
                            $model['device'],
                            $file,
                            $brand,
                            $model['regex'],
                            \implode(PHP_EOL, $availableDeviceTypeNames)
                        ));
                    }
                } else {
                    $this->assertArrayHasKey('device', $regex);
                    $this->assertArrayHasKey('model', $regex);
                    $this->assertIsString($regex['model']);
                }
            }
        }
    }

    public function testSetCacheInvalid(): void
    {
        $this->expectException(\TypeError::class);
        $dd = new DeviceDetector();
        $dd->setCache('Invalid');
    }

    public function testCacheSetAndGet(): void
    {
        if (!\extension_loaded('memcached') || !\class_exists('\Doctrine\Common\Cache\MemcachedCache')) {
            $this->markTestSkipped('memcached not enabled');
        }

        $dd            = new DeviceDetector();
        $memcached     = new \Memcached();
        $doctrineCache = new MemcachedCache();
        $doctrineCache->setMemcached($memcached);
        $dd->setCache(new DoctrineBridge($doctrineCache));
        $this->assertInstanceOf(DoctrineBridge::class, $dd->getCache());
    }

    public function testParseEmptyUA(): void
    {
        $dd = new DeviceDetector('');
        $dd->parse();
        $dd->parse(); // call second time completes code coverage
        $this->assertFalse($dd->isDesktop());
        $this->assertFalse($dd->isMobile());
    }

    public function testParseInvalidUA(): void
    {
        $dd = new DeviceDetector('12345');
        $dd->parse();
        $this->assertFalse($dd->isDesktop());
        $this->assertFalse($dd->isMobile());
    }

    public function testIsParsed(): void
    {
        $dd = new DeviceDetector('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36');
        $this->assertFalse($dd->isParsed());
        $dd->parse();
        $this->assertTrue($dd->isParsed());
    }

    /**
     * @dataProvider getFixtures
     */
    public function testParse(array $fixtureData): void
    {
        $ua          = $fixtureData['user_agent'];
        $clientHints = !empty($fixtureData['headers']) ? ClientHints::factory($fixtureData['headers']) : null;

        AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

        try {
            $uaInfo = DeviceDetector::getInfoFromUserAgent($ua, $clientHints);
        } catch (\Exception $exception) {
            throw new \Exception(
                \sprintf('Error: %s from useragent %s', $exception->getMessage(), $ua),
                $exception->getCode(),
                $exception
            );
        }

        $errorMessage = \sprintf(
            "UserAgent: %s\nHeaders: %s",
            $ua,
            \print_r($fixtureData['headers'] ?? null, true)
        );

        unset($fixtureData['headers']); // ignore headers in result

        $this->assertEquals($fixtureData, $uaInfo, $errorMessage);
    }

    public function getFixtures(): array
    {
        $fixtures     = [];
        $fixtureFiles = \glob(\realpath(__DIR__) . '/fixtures/*.yml');

        foreach ($fixtureFiles as $fixturesPath) {
            $typeFixtures = \Spyc::YAMLLoad($fixturesPath);
            $deviceType   = \str_replace('_', ' ', \substr(\basename($fixturesPath), 0, -4));

            if ('bots' === $deviceType) {
                continue;
            }

            $fixtures = \array_merge(\array_map(static function ($elem) {
                return [$elem];
            }, $typeFixtures), $fixtures);
        }

        return $fixtures;
    }

    public function testInstanceReusage(): void
    {
        $userAgents = [
            'Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36' => [
                'device' => [
                    'brand' => 'Archos',
                    'model' => '101 PLATINUM',
                ],
            ],
            'Opera/9.80 (Linux mips; U; HbbTV/1.1.1 (; Vestel; MB95; 1.0; 1.0; ); en) Presto/2.10.287 Version/12.00'                                        => [
                'device' => [
                    'brand' => 'Vestel',
                    'model' => '',
                ],
            ],
            'Sraf/3.0 (Linux i686 ; U; HbbTV/1.1.1 (+PVR+DL;NEXUS; TV44; sw1.0) CE-HTML/1.0 Config(L:eng,CC:DEU); en/de)'                                   => [
                'device' => [
                    'brand' => '',
                    'model' => '',
                ],
            ],
        ];

        $deviceDetector = new DeviceDetector();

        foreach ($userAgents as $userAgent => $expected) {
            $deviceDetector->setUserAgent($userAgent);
            $deviceDetector->parse();
            $this->assertEquals($expected['device']['brand'], $deviceDetector->getBrandName());
            $this->assertEquals($expected['device']['model'], $deviceDetector->getModel());
        }
    }

    /**
     * @dataProvider getVersionTruncationFixtures
     */
    public function testVersionTruncation(string $useragent, int $truncationType, string $osVersion, string $clientVersion): void
    {
        AbstractParser::setVersionTruncation($truncationType);
        $dd = new DeviceDetector($useragent);
        $dd->parse();
        $this->assertEquals($osVersion, $dd->getOs('version'));
        $this->assertEquals($clientVersion, $dd->getClient('version'));
        AbstractParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
    }

    public function getVersionTruncationFixtures(): array
    {
        return [
            ['Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', AbstractParser::VERSION_TRUNCATION_NONE, '4.2.2', '34.0.1847.114'],
            ['Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', AbstractParser::VERSION_TRUNCATION_BUILD, '4.2.2', '34.0.1847.114'],
            ['Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', AbstractParser::VERSION_TRUNCATION_PATCH, '4.2.2', '34.0.1847'],
            ['Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', AbstractParser::VERSION_TRUNCATION_MINOR, '4.2', '34.0'],
            ['Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36', AbstractParser::VERSION_TRUNCATION_MAJOR, '4', '34'],
        ];
    }

    public function testNotSkipDetectDeviceForClientHints(): void
    {
        $dd = $this->createPartialMock(Mobile::class, ['hasDesktopFragment']);

        $dd->expects($this->once())->method('hasDesktopFragment')->willReturn(true);

        // simulate work not use clienthints
        $dd->setUserAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.53 Safari/537.36');

        $this->assertEquals($dd->parse(), [
            'deviceType' => null,
            'model'      => '',
            'brand'      => '',
        ]);

        // simulate work use clienthint + model
        $dd->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36 Edg/103.0.1264.44');
        $dd->setClientHints(new ClientHints(
            'Galaxy 4',
            'Android',
            '8.0.5',
            '103.0.0.0',
            [
                ['brand' => ' Not A;Brand', 'version' => '103.0.0.0'],
                ['brand' => 'Chromium', 'version' => '103.0.0.0'],
                ['brand' => 'Chrome', 'version' => '103.0.0.0'],
            ],
            true,
            '',
            '',
            ''
        ));

        $this->assertEquals($dd->parse(), [
            'deviceType' => null,
            'model'      => 'Galaxy 4',
            'brand'      => '',
        ]);
    }

    public function testVersionTruncationForClientHints(): void
    {
        AbstractParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_MINOR);
        $dd = new DeviceDetector();
        $dd->setClientHints(new ClientHints(
            'Galaxy 4',
            'Android',
            '8.0.5',
            '98.0.14335.105',
            [
                ['brand' => ' Not A;Brand', 'version' => '99.0.0.0'],
                ['brand' => 'Chromium', 'version' => '98.0.14335.105'],
                ['brand' => 'Chrome', 'version' => '98.0.14335.105'],
            ],
            true,
            '',
            '',
            ''
        ));
        $dd->parse();
        $this->assertEquals('8.0', $dd->getOs('version'));
        $this->assertEquals('98.0', $dd->getClient('version'));
        AbstractParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
    }

    /**
     * @dataProvider getBotFixtures
     */
    public function testParseBots(array $fixtureData): void
    {
        $ua = $fixtureData['user_agent'];
        $dd = new DeviceDetector($ua);
        $dd->parse();
        $this->assertTrue($dd->isBot());
        $botData = $dd->getBot();
        $this->assertEquals($botData, $fixtureData['bot']);
        // client and os will always be unknown for bots
        $this->assertEquals($dd->getOs('short_name'), DeviceDetector::UNKNOWN);
        $this->assertEquals($dd->getClient('short_name'), DeviceDetector::UNKNOWN);

        if (!\array_key_exists('category', $botData) || '' === $botData['category']) {
            return;
        }

        $categories = [
            'Benchmark',
            'Crawler',
            'Feed Fetcher',
            'Feed Parser',
            'Feed Reader',
            'Network Monitor',
            'Read-it-later Service',
            'Search bot',
            'Search tools',
            'Security Checker',
            'Security search bot',
            'Service Agent',
            'Service bot',
            'Site Monitor',
            'Social Media Agent',
            'Validator',
        ];

        $this->assertTrue(
            \in_array($botData['category'], $categories, true),
            \sprintf(
                "Unknown category: \"%s\"\nUseragent: %s\nAvailable categories:\n%s\n",
                $botData['category'],
                $ua,
                \implode(PHP_EOL, $categories)
            )
        );
    }

    public function getBotFixtures(): array
    {
        $fixturesPath = \realpath(__DIR__ . '/fixtures/bots.yml');
        $fixtures     = \Spyc::YAMLLoad($fixturesPath);

        return \array_map(static function ($elem) {
            return [$elem];
        }, $fixtures);
    }

    public function testGetInfoFromUABot(): void
    {
        $expected = [
            'user_agent' => 'Googlebot/2.1 (http://www.googlebot.com/bot.html)',
            'bot'        => [
                'name'     => 'Googlebot',
                'category' => 'Search bot',
                'url'      => 'http://www.google.com/bot.html',
                'producer' => [
                    'name' => 'Google Inc.',
                    'url'  => 'http://www.google.com',
                ],
            ],
        ];
        $this->assertEquals($expected, DeviceDetector::getInfoFromUserAgent($expected['user_agent']));
    }

    public function testParseNoDetails(): void
    {
        $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
        $dd        = new DeviceDetector($userAgent);
        $dd->discardBotInformation();
        $dd->parse();
        $this->assertEquals([true], $dd->getBot());
    }

    public function testMagicMMethods(): void
    {
        $ua = 'Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.136 Mobile Safari/537.36';
        $dd = new DeviceDetector($ua);
        $dd->parse();
        $this->assertTrue($dd->isSmartphone());
        $this->assertFalse($dd->isFeaturePhone());
        $this->assertFalse($dd->isTablet());
        $this->assertFalse($dd->isPhablet());
        $this->assertFalse($dd->isCarBrowser());
        $this->assertFalse($dd->isSmartDisplay());
        $this->assertFalse($dd->isTV());
        $this->assertFalse($dd->isConsole());
        $this->assertFalse($dd->isPortableMediaPlayer());
        $this->assertFalse($dd->isCamera());

        $this->assertTrue($dd->isBrowser());
        $this->assertFalse($dd->isLibrary());
        $this->assertFalse($dd->isMediaPlayer());
        $this->assertFalse($dd->isMobileApp());
        $this->assertFalse($dd->isPIM());
        $this->assertFalse($dd->isFeedReader());
    }

    public function testInvalidMagicMethod(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $dd = new DeviceDetector('Mozilla/5.0');
        $dd->parse();
        $dd->inValidMethod();
    }

    public function testGetOs(): void
    {
        $dd = new DeviceDetector('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
        $this->assertNull($dd->getOs());
        $dd->parse();
        $expected = [
            'name'       => 'Windows',
            'short_name' => 'WIN',
            'version'    => '7',
            'platform'   => 'x64',
            'family'     => 'Windows',
        ];
        $this->assertEquals($expected, $dd->getOs());
    }

    public function testGetClient(): void
    {
        $dd = new DeviceDetector('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
        $this->assertNull($dd->getClient());
        $dd->parse();
        $expected = [
            'type'           => 'browser',
            'name'           => 'Internet Explorer',
            'short_name'     => 'IE',
            'version'        => '9.0',
            'engine'         => 'Trident',
            'engine_version' => '5.0',
            'family'         => 'Internet Explorer',
        ];
        $this->assertEquals($expected, $dd->getClient());
    }

    public function getTypeMethodFixtures(): array
    {
        $fixturePath = \realpath(__DIR__ . '/Parser/fixtures/type-methods.yml');

        return \Spyc::YAMLLoad($fixturePath);
    }

    /**
     * @dataProvider getTypeMethodFixtures
     */
    public function testTypeMethods(string $ua, array $checkTypes): void
    {
        try {
            $dd = $this->getDeviceDetector();
            $dd->discardBotInformation();
            $dd->setUserAgent($ua);
            $dd->parse();
        } catch (\Exception $exception) {
            throw new \Exception(
                \sprintf('Error: %s from useragent %s', $exception->getMessage(), $ua),
                $exception->getCode(),
                $exception
            );
        }

        $this->assertEquals([
            $dd->isBot(), $dd->isMobile(), $dd->isDesktop(),
            $dd->isTablet(), $dd->isTV(), $dd->isWearable(),
        ], $checkTypes, \sprintf(
            "test: %s\nfrom useragent %s",
            '[isBot(), isMobile(), isDesktop(), isTablet(), isTV(), isWearable()]',
            $ua
        ));
    }

    public function testGetBrandName(): void
    {
        $dd = new DeviceDetector('Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.136 Mobile Safari/537.36');
        $dd->parse();
        $this->assertEquals('Google', $dd->getBrandName());
    }

    public function testGetBrand(): void
    {
        $dd = new DeviceDetector('Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.136 Mobile Safari/537.36');
        $dd->parse();
        $this->assertEquals('GO', $dd->getBrand());
    }

    public function testIsTouchEnabled(): void
    {
        $dd = new DeviceDetector('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; ARM; Trident/6.0; Touch; ARMBJS)');
        $dd->parse();
        $this->assertTrue($dd->isTouchEnabled());
    }

    public function testSkipBotDetection(): void
    {
        $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        $dd = new DeviceDetector($ua);
        $dd->parse();
        $this->assertFalse($dd->isMobile());
        $this->assertTrue($dd->isBot());
        $dd = new DeviceDetector($ua);
        $dd->skipBotDetection();
        $dd->parse();
        $this->assertTrue($dd->isMobile());
        $this->assertFalse($dd->isBot());
    }

    public function testSetYamlParserInvalid(): void
    {
        $this->expectException(\TypeError::class);

        $dd = new DeviceDetector();
        $dd->setYamlParser('Invalid');
    }

    public function testSetYamlParser(): void
    {
        $reader = function & ($object, $property) {
            $value = & Closure::bind(function & () use ($property) {
                return $this->$property;
            }, $object, $object)->__invoke();

            return $value;
        };

        $dd = new DeviceDetector();
        $dd->setYamlParser(new Symfony());
        $dd->setUserAgent('Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36');

        $dd->parse();
        $this->assertTrue($dd->isMobile());
        $this->assertInstanceOf(Symfony::class, $dd->getYamlParser());

        foreach ($dd->getClientParsers() as $parser) {
            if ($parser instanceof MobileApp) {
                $appHints = & $reader($parser, 'appHints');
                $this->assertInstanceOf(Symfony::class, $appHints->getYamlParser());
            }

            if (!($parser instanceof Browser)) {
                continue;
            }

            $browserHints = & $reader($parser, 'browserHints');
            $this->assertInstanceOf(Symfony::class, $browserHints->getYamlParser());
        }
    }

    public function testCheckRegexRestrictionEndCondition(): void
    {
        $this->assertTrue($this->checkRegexRestrictionEndCondition('([^;/)]+)[;/)]'), 'skip condition');
        $this->assertTrue($this->checkRegexRestrictionEndCondition('([^/;)]+)[;/)]'), 'skip condition');
        $this->assertFalse($this->checkRegexRestrictionEndCondition('TestValue[;/)]'), 'bad condition');
        $this->assertFalse($this->checkRegexRestrictionEndCondition('TestValue[/;)]'), 'bad condition');
        $this->assertTrue($this->checkRegexRestrictionEndCondition('TestValue(?:[);/ ]|$)'), 'pass condition');
        $this->assertTrue($this->checkRegexRestrictionEndCondition('TestValue(?:[/); ]|$)'), 'pass condition');
        $this->assertTrue($this->checkRegexRestrictionEndCondition('TestValue(?:[);/]|$)'), 'pass condition');
        $this->assertTrue($this->checkRegexRestrictionEndCondition('TestValue(?:[;)/]|$)'), 'pass condition');
    }

    /**
     * Checks the AbstractDeviceParser::$deviceBrands for duplicate brands
     */
    public function testDuplicateBrands(): void
    {
        $brands     = \array_map('strtolower', AbstractDeviceParser::$deviceBrands);
        $unique     = \array_unique($brands);
        $duplicates = \array_diff_assoc($brands, $unique);

        $this->assertCount(0, $duplicates, \sprintf(
            'Duplicate brands exists: %s',
            \print_r($duplicates, true)
        ));
    }

    /**
     * check the Symfony parser for fixtures parsing errors
     */
    public function testSymfonyParser(): void
    {
        $files       = \array_merge(
            \glob(__DIR__ . '/../regexes/client/*.yml'),
            \glob(__DIR__ . '/../regexes/device/*.yml'),
            \glob(__DIR__ . '/../regexes/*.yml')
        );
        $yamlSymfony = new Symfony();

        foreach ($files as $file) {
            $yamlSymfony->parseFile($file);
        }

        $this->expectNotToPerformAssertions();
    }

    /**
     * check the regular expression for the vertical line closing the group
     * @param string $regexString
     *
     * @return bool
     */
    protected function checkRegexVerticalLineClosingGroup(string $regexString): bool
    {
        if (false !== \strpos($regexString, '|)')) {
            return !\preg_match('#(?<!\\\)(\|\))#is', $regexString);
        }

        return true;
    }

    /**
     * check the regular expression for end condition constraint (?:[);/ ]|$)
     *
     * @param string $regexString
     *
     * @return bool
     */
    protected function checkRegexRestrictionEndCondition(string $regexString): bool
    {
        // get conditions [;)\ ]
        if (\preg_match_all('~(\[[);\\\ ]{4}\])~m', $regexString, $matches1)) {
            return false;
        }

        // get conditions [);/ ]
        if (\preg_match_all('~(?<!(?:\(\[\^[;\/)]{3}\][\+\*]\)))(\[[);\/ ]{3,4}\])~m', $regexString, $matches1)) {
            // get conditions (?:[);/ ]|$)
            if (!\preg_match_all('~(?:(?<=(?:\?:))(\[[);\/ ]{3,4}\])(?=\|\$))~m', $regexString, $matches2)) {
                return false;
            }

            return \count($matches1[0]) === \count($matches2[1]);
        }

        return true;
    }

    private function getDeviceDetector(): DeviceDetector
    {
        static $dd;

        if (null === $dd) {
            $dd = new DeviceDetector();
        }

        return $dd;
    }
}
