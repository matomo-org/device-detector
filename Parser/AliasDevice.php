<?php declare(strict_types=1);
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

/**
 * Class AliasDevice
 * @package DeviceDetector\Parser
 * @example ```php
 *      use DeviceDetector\Parser\AliasDevice;
 *
 *      $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
 *      $parser = new AliasDevice;
 *      $parser->setUserAgent($userAgent);
 *      $result = $parser->parse();
 *      var_dump($result);
 * ```
 */
class AliasDevice extends AbstractParser
{
    protected $fixtureFile = 'regexes/alias_devices.yml';
    protected $parserName  = 'alias_device';

    private $brandReplaceRegexp = null;

    /**
     * @return array
     * @throws \Exception
     */
    public function parse(): array
    {
        $matches = false;

        $find = [];
        foreach ($this->getRegexes() as $aliasDeviceRegex) {
            $find = $aliasDeviceRegex;
            $matches = $this->matchUserAgent($find['regex']);
            if ($matches) {
                break;
            }
        }

        if (!$matches) {
            return [];
        }

        $name = $this->buildByMatch($find['name'], $matches);
        $name = preg_replace($this->getBrandReplaceRegexp(), '', $name);
        $name = trim($name);

        return compact('name');
    }

    /**
     * @return null|string
     */
    private function getBrandReplaceRegexp()
    {
        if (empty($this->brandReplaceRegexp)) {
            $cacheKey = sprintf('DeviceDetector-%s-brands-regexp', DeviceDetector::VERSION);
            $this->brandReplaceRegexp = $this->getCache()->fetch($cacheKey);
            if (empty($this->brandReplaceRegexp)) {
                $escapeeChars = ['+' => '\+', '.' => '\.'];
                $brands = implode('|', array_values(AbstractDeviceParser::$deviceBrands));
                $brands = str_replace(array_keys($escapeeChars), array_values($escapeeChars), $brands);
                $pattern = sprintf('#([ _-]?(?:%s)[ _-]?)#i', $brands);

                $this->brandReplaceRegexp = $pattern;
                $this->getCache()->save($cacheKey, $this->brandReplaceRegexp);
            }
        }
        return $this->brandReplaceRegexp;
    }
}
