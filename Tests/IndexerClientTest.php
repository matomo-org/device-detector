<?php

declare(strict_types=1);

namespace DeviceDetector\Tests;

use DeviceDetector\Parser\IndexerClient;
use PHPUnit\Framework\TestCase;

class IndexerClientTest extends TestCase
{
    public function getUserAgents(): array
    {
        return [
            [
                'Mozilla/5.0 (Linux; Android 4.2.2; ARCHOS 101 PLATINUM Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Safari/537.36',
                'ifaba664',
                'mozilla.applewebkit.chrome.safari',
            ],
            [
                'AndroidDownloadManager/4.1.1 (Linux; U; Android 4.1.1; MB886 Build/9.8.0Q-97_MB886_FFW-20)',
                'ic7815bd6',
                'androiddownloadmanager',
            ],
            [
                'Mozilla/5.0 (Linux; Android 9.0; V17 SD665) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 EdgA/80.0.361.109',
                'ia2420cff',
                'mozilla.applewebkit.chrome.mobile.safari.edga',
            ],
            [
                'Mozilla/5.0 (Linux; U; Android 11; en-US; veux Build/RKQ1.211001.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/100.0.4896.58 UCBrowser/13.7.5.1321 Mobile Safari/537.36',
                'i426141aa',
                'mozilla.applewebkit.version.chrome.ucbrowser.mobile.safari',
            ],
            [
                'Mozilla/5.0 (Windows NT 10.0.18362.356; osmeta 10.3.45364) AppleWebKit/602.1.1 (KHTML, like Gecko) Version/9.0 Safari/602.1.1 osmeta/10.3.45364 Build/45364 FBAN/FBW;FBAV/186.0.0.88.783;FBBV/171658047;FBDV/AlienwareAuroraR70858;FBMD/Alienware Aurora R7 0858;FBSN/Windows;FBSV/10.0.18362.657;FBSS/2;FBCR/;FBID/desktop;FBLC/en_US;FBOP/45;FBRV/0',
                'i1817d656',
                'mozilla.applewebkit.version.safari.osmeta.build.aurora.r7',
            ],
        ];
    }

    /**
     * @dataProvider getUserAgents
     */
    public function testCreateDataIndex(string $useragent, string $hash, string $path): void
    {
        $data    = IndexerClient::createDataIndex($useragent);
        $message = 'Structure data ' . \print_r($data, true);

        $this->assertEquals($hash, $data['hash'], $message);
        $this->assertEquals($path, $data['path'], $message);
    }

    public function testFindIndex(): void
    {
        $indexerClient = new IndexerClient();
        $indexerClient->setUserAgent('Mozilla/5.0 (Linux; U; Android 11; en-US; veux Build/RKQ1.211001.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/100.0.4896.58 UCBrowser/13.7.5.1321 Mobile Safari/537.36');
        $result = $indexerClient->parse();

        $this->assertArrayHasKey('hash', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey(IndexerClient::BROWSER, $result['data']);
    }
}
