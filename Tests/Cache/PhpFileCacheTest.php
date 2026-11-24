<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Tests\Cache;

use DeviceDetector\Cache\PhpFileCache;
use DeviceDetector\DeviceDetector;
use PHPUnit\Framework\TestCase;

class PhpFileCacheTest extends TestCase
{
    /**
     * @var string
     */
    private $directory;

    protected function setUp(): void
    {
        $this->directory = \sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dd-php-file-cache-' . \uniqid();
    }

    protected function tearDown(): void
    {
        foreach (\glob($this->directory . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            \unlink($file);
        }

        if (!\is_dir($this->directory)) {
            return;
        }

        \rmdir($this->directory);
    }

    public function testSetNotPresent(): void
    {
        $cache = new PhpFileCache($this->directory);
        $this->assertFalse($cache->fetch('NotExistingKey'));
        $this->assertFalse($cache->contains('NotExistingKey'));
    }

    public function testSetAndGet(): void
    {
        $cache = new PhpFileCache($this->directory);

        // add entry
        $cache->save('key', 'value');
        $this->assertTrue($cache->contains('key'));
        $this->assertEquals('value', $cache->fetch('key'));

        // change entry
        $cache->save('key', 'value2');
        $this->assertEquals('value2', $cache->fetch('key'));

        // remove entry
        $cache->delete('key');
        $this->assertFalse($cache->fetch('key'));

        // flush all entries
        $cache->save('key', 'value2');
        $cache->save('key3', 'value2');
        $cache->flushAll();
        $this->assertFalse($cache->fetch('key'));
        $this->assertFalse($cache->fetch('key3'));
    }

    public function testEntriesAreStoredAsPhpFiles(): void
    {
        $data = ['regex' => 'Foo/(\d+)\'"$1', 'list' => [1, 2.5, true, null], 'brand' => 'Völfen'];

        (new PhpFileCache($this->directory))->save('DeviceDetector-1.0-regexes-mobiles', $data);

        $files = \glob($this->directory . DIRECTORY_SEPARATOR . '*.php') ?: [];
        $this->assertCount(1, $files);
        $this->assertSame([0, $data], include $files[0]);
    }

    public function testFlushAllKeepsForeignFiles(): void
    {
        \mkdir($this->directory);
        $foreignFile = $this->directory . DIRECTORY_SEPARATOR . 'foreign.php';
        \file_put_contents($foreignFile, '<?php return 1;');

        $cache = new PhpFileCache($this->directory);
        $cache->save('key', 'value');
        $cache->flushAll();

        $this->assertFileExists($foreignFile);
    }

    public function testNonExportableDataIsSerialized(): void
    {
        $data = ['object' => new \ArrayObject([1, 2])];

        (new PhpFileCache($this->directory))->save('key', $data);

        $files = \glob($this->directory . DIRECTORY_SEPARATOR . '*.php') ?: [];
        $this->assertEquals([0, $data], include $files[0]);
    }

    public function testExpiredEntryIsNotReturned(): void
    {
        $cache = new PhpFileCache($this->directory);
        $cache->save('key', 'value', 60);
        $this->assertEquals('value', $cache->fetch('key'));

        // recreate the file of the entry as written by another process, but already expired
        $files = \glob($this->directory . DIRECTORY_SEPARATOR . '*.php') ?: [];
        $cache->flushAll();
        \file_put_contents($files[0], \sprintf('<?php return [%d, "value"];', \time() - 1));

        $this->assertFalse($cache->fetch('key'));
        $this->assertFalse($cache->contains('key'));
    }

    public function testDeviceDetectorUsesCache(): void
    {
        $userAgent = 'Mozilla/5.0 (Linux; Android 13; SM-S911B) AppleWebKit/537.36 (KHTML, like Gecko) '
            . 'Chrome/120.0.0.0 Mobile Safari/537.36';

        $dd = new DeviceDetector($userAgent);
        $dd->setCache(new PhpFileCache($this->directory));
        $dd->parse();
        $expected = [$dd->getOs(), $dd->getClient(), $dd->getDeviceName(), $dd->getBrandName(), $dd->getModel()];

        $this->assertNotEmpty(\glob($this->directory . DIRECTORY_SEPARATOR . '*.php'));

        $dd = new DeviceDetector($userAgent);
        $dd->setCache(new PhpFileCache($this->directory));
        $dd->parse();

        $this->assertSame(
            $expected,
            [$dd->getOs(), $dd->getClient(), $dd->getDeviceName(), $dd->getBrandName(), $dd->getModel()]
        );
    }
}
