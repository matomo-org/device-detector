<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Cache;

/**
 * Class PhpFileCache
 *
 * Cache that stores every entry as a PHP file returning the cached data (exported using var_export).
 * With opcache enabled the compiled files are kept in shared memory, so loading the parsed regexes
 * in a new process (e.g. every PHP-FPM request) does not require parsing the YAML files again.
 * Loaded entries are additionally kept in memory for the lifetime of the process.
 */
class PhpFileCache implements CacheInterface
{
    /**
     * Prefix of all cache files, used to only remove own files in flushAll()
     */
    private const FILE_PREFIX = 'devicedetector-';

    /**
     * Entries loaded in this process, indexed by file name
     * Each entry consists of the expiration timestamp (0 = never) and the cached data
     * @var array<string, array>
     */
    private static $loaded = [];

    /**
     * @var string
     */
    private $directory;

    /**
     * PhpFileCache constructor.
     *
     * @param string $directory directory to store the cache files in, will be created if it does not exist
     */
    public function __construct(string $directory)
    {
        $this->directory = \rtrim($directory, '/\\');
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id)
    {
        $entry = $this->load($this->getFileName($id));

        return null !== $entry ? $entry[1] : false;
    }

    /**
     * @inheritdoc
     */
    public function contains(string $id): bool
    {
        return null !== $this->load($this->getFileName($id));
    }

    /**
     * @inheritdoc
     */
    public function save(string $id, $data, int $lifeTime = 0): bool
    {
        $file    = $this->getFileName($id);
        $expires = $lifeTime > 0 ? \time() + $lifeTime : 0;

        // var_export can only restore arrays and scalars, everything else is stored serialized
        $export = $this->isExportable($data)
            ? \var_export($data, true)
            : 'unserialize(' . \var_export(\serialize($data), true) . ')';

        $content = \sprintf("<?php\n\nreturn [%d, %s];\n", $expires, $export);

        // kept in memory even if the file can't be written, so the data doesn't need to be built again in this process
        self::$loaded[$file] = [$expires, $data];

        return $this->write($file, $content);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $id): bool
    {
        $file = $this->getFileName($id);

        unset(self::$loaded[$file]);

        return $this->remove($file);
    }

    /**
     * @inheritdoc
     */
    public function flushAll(): bool
    {
        $result = true;

        foreach (\glob($this->directory . DIRECTORY_SEPARATOR . self::FILE_PREFIX . '*.php') ?: [] as $file) {
            unset(self::$loaded[$file]);
            $result = $this->remove($file) && $result;
        }

        return $result;
    }

    /**
     * Returns the entry stored in the given file, or null if it does not exist or is expired
     *
     * @param string $file
     *
     * @return array|null
     */
    private function load(string $file): ?array
    {
        if (!isset(self::$loaded[$file])) {
            if (!\is_file($file)) {
                return null;
            }

            $entry = (static function (string $file) {
                return include $file;
            })($file);

            if (!\is_array($entry) || !\array_key_exists(1, $entry)) {
                return null;
            }

            self::$loaded[$file] = $entry;
        }

        $entry = self::$loaded[$file];

        if (0 !== $entry[0] && $entry[0] < \time()) {
            unset(self::$loaded[$file]);

            return null;
        }

        return $entry;
    }

    /**
     * Writes the content to the given file atomically, so concurrent processes never include a partial file
     *
     * @param string $file
     * @param string $content
     *
     * @return bool
     */
    private function write(string $file, string $content): bool
    {
        if (!\is_dir($this->directory) && !@\mkdir($this->directory, 0777, true) && !\is_dir($this->directory)) {
            return false;
        }

        $tmpFile = @\tempnam($this->directory, self::FILE_PREFIX . 'tmp');

        if (false === $tmpFile) {
            return false;
        }

        if (false === @\file_put_contents($tmpFile, $content)) {
            @\unlink($tmpFile);

            return false;
        }

        @\chmod($tmpFile, 0666 & ~\umask());

        if (!@\rename($tmpFile, $file)) {
            @\unlink($tmpFile);

            return false;
        }

        $this->invalidateOpcache($file);

        return true;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private function remove(string $file): bool
    {
        $this->invalidateOpcache($file);

        return @\unlink($file) || !\is_file($file);
    }

    /**
     * @param string $file
     */
    private function invalidateOpcache(string $file): void
    {
        if (!\function_exists('opcache_invalidate')) {
            return;
        }

        @\opcache_invalidate($file, true);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function getFileName(string $id): string
    {
        // the id is hashed, as it may contain characters not allowed in file names
        return $this->directory . DIRECTORY_SEPARATOR . self::FILE_PREFIX . \md5($id) . '.php';
    }

    /**
     * Returns if the data only consists of arrays and scalars, so it can be restored from var_export
     *
     * @param mixed $data
     *
     * @return bool
     */
    private function isExportable($data): bool
    {
        if (null === $data || \is_scalar($data)) {
            return true;
        }

        if (!\is_array($data)) {
            return false;
        }

        foreach ($data as $value) {
            if (!$this->isExportable($value)) {
                return false;
            }
        }

        return true;
    }
}
