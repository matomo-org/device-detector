<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

class AliasDevice extends AbstractParser
{
    protected $fixtureFile = 'regexes/alias_devices.yml';

    protected $parserName = 'alias_device';

    private $brandReplaceRegexp = null;

    public $brandReplace = true;

    /**
     * @return array|null
     *
     * @throws \Exception
     */
    public function parse(): ?array
    {
        $this->userAgent = \rawurldecode($this->userAgent);

        $matches = false;
        $find    = [];

        foreach ($this->getRegexes() as $aliasDeviceRegex) {
            $find    = $aliasDeviceRegex;
            $matches = $this->matchUserAgent($find['regex']);

            if ($matches) {
                break;
            }
        }

        if (!$matches) {
            return null;
        }

        $name = $this->buildByMatch($find['name'], $matches);

        if (true === $this->brandReplace) {
            $name = \preg_replace($this->getBrandReplaceRegexp(), '', $name);
        }

        $name = \trim($name);

        return \compact('name');
    }

    /**
     * @return string|null
     */
    private function getBrandReplaceRegexp(): ?string
    {
        if (empty($this->brandReplaceRegexp)) {
            $cacheKey                 = \sprintf('DeviceDetector-%s-brands-regexp', DeviceDetector::VERSION);
            $this->brandReplaceRegexp = $this->getCache()->fetch($cacheKey);

            if (empty($this->brandReplaceRegexp)) {
                $replaceBrands = \array_merge([
                    'HUAWEI HUAWEI',
                ], \array_values(AbstractDeviceParser::$deviceBrands));

                $escapeeChars = ['+' => '\+', '.' => '\.'];
                $brands       = \implode('|', $replaceBrands);
                $brands       = \str_replace(\array_keys($escapeeChars), \array_values($escapeeChars), $brands);
                $pattern      = \sprintf('#(?:^|[^A-Z0-9-_]|[^A-Z0-9-]_|sprd-)(%s)[ _]#i', $brands);

                $this->brandReplaceRegexp = $pattern;
                $this->getCache()->save($cacheKey, $this->brandReplaceRegexp);
            }
        }

        return $this->brandReplaceRegexp;
    }
}
