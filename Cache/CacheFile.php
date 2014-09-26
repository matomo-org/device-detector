<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Cache;

/**
 * Class CacheFile
 *
 * Extends the static caching with a caching in files
 *
 * @package DeviceDetector\Cache
 */
class CacheFile extends CacheStatic implements CacheInterface
{
    /**
     * Path to cache directory
     * @var string
     */
    protected $cachePath;

    /**
     * @param string $directory path to save cache files in
     */
    public function __construct($directory)
    {
        $this->cachePath = realpath($directory);
    }

    public function set($key, $value)
    {
        parent::set($key, $value);

        if (empty($key)) {
            return false;
        }
        if (!is_writable($this->cachePath)) {
            return false;
        }

        $id = $this->getCacheFileName($key);

        if (is_object($value)) {
            throw new \Exception('You cannot use the CacheFile to cache an object, only arrays, strings and numbers.');
        }

        $cache_literal = "<" . "?php\n";
        $cache_literal .= "$" . "content   = " . var_export($value, true) . ";\n";
        $cache_literal .= "$" . "cache_complete   = true;\n";
        $cache_literal .= "?" . ">";

        // Write cache to a temp file, then rename it, overwriting the old cache
        // On *nix systems this should guarantee atomicity
        $tmp_filename = tempnam($this->cachePath, 'tmp_');
        @chmod($tmp_filename, 0640);
        if ($fp = @fopen($tmp_filename, 'wb')) {
            @fwrite($fp, $cache_literal, strlen($cache_literal));
            @fclose($fp);

            if (!@rename($tmp_filename, $id)) {
                // On some systems rename() doesn't overwrite destination
                // @codeCoverageIgnoreStart
                @unlink($id);
                if (!@rename($tmp_filename, $id)) {
                    // Make sure that no temporary file is left over
                    // if the destination is not writable
                    @unlink($tmp_filename);
                    return false;
                }
            }
            // @codeCoverageIgnoreEnd

            // invalidate opcache for file if opcache is active
            $this->opCacheInvalidate($id);

            return true;
        }
        return false;

    }

    public function get($key)
    {
        $value = parent::get($key);

        if (is_null($value)) {
            $cache_complete = false;
            $content = '';

            // We are assuming that most of the time cache will exists
            $cacheFilePath = $this->getCacheFileName($key);

            $ok = @include($cacheFilePath);

            if ($ok && $cache_complete == true) {

                // as key was missing in "parent" cache, set it again
                parent::set($key, $content);
                return $content;
            }
        }

        return $value;
    }

    protected function getCacheFileName($id)
    {
        return sprintf('%s/%s.php', $this->cachePath, $id);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function opCacheInvalidate($filepath)
    {
        if (function_exists('opcache_invalidate')
            && is_file($filepath)
        ) {
            @opcache_invalidate($filepath, $force = true);
        }
    }
}
