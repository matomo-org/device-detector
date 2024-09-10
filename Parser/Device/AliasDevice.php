<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

declare(strict_types=1);

namespace DeviceDetector\Parser\Device;

use DeviceDetector\Parser\AbstractParser;

class AliasDevice extends AbstractParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/alias-device.yml';

    /**
     * @var string
     */
    protected $parserName = 'alias device';

    /**
     * @var bool
     */
    protected $replaceBrand = true;


    /**
     * @return array|null
     */
    public function parse(): ?array
    {
        $name = null;
        $this->userAgent = $this->restoreUserAgent($this->userAgent);

        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);

            if ($matches) {
                $name = $this->buildByMatch($regex['name'], $matches);

                break;
            }
        }

        if (null !== $name && $this->replaceBrand) {
            $customBrands = ['HUAWEI HUAWEI',  'viv-vivo'];
            $brands       = \array_merge($customBrands, array_values(AbstractDeviceParser::$deviceBrands));
            $replaceRegex = '~(?:^|[^A-Z0-9-_]|[^A-Z0-9-]_|sprd-)(' . \implode('|', $brands) . ')[ _]~is';
            $name         = \preg_replace($replaceRegex, '', $name);
        }

        return \compact('name');
    }

    /**
     * @param string $userAgent
     * @return string
     */
    protected function restoreUserAgent(string $userAgent): string
    {
        $userAgent  = rawurldecode($userAgent);
        $hasReplace = preg_match('~ip(?:ad|hone): build/~i', $userAgent) && preg_match('~android ~i', $userAgent);

        if ($hasReplace) {
            $userAgent = preg_replace('~;ip(?:ad|hone):~i', '', $userAgent);
        }

        return $userAgent;
    }

    /**
     * @param string $regex
     * @return string
     */
    protected function createUserAgentRegex(string $regex): string
    {
        $regex = \preg_replace('/\//', '\\/', $regex);
        $regex = \preg_replace('/\+\+/', '+', $regex);

        return '~(?:' . $regex . ')~i';
    }
}