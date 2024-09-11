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
    protected $replaceBrand = false;

    /**
     * Parse current the UA to extract device code
     *
     * @return array
     */
    public function parse(): array
    {
        $name            = null;
        $this->userAgent = $this->restoreUserAgent($this->userAgent);
        $model           = $this->clientHints ? $this->clientHints->getModel() : '';

        if ('' !== $model) {
            $name = $model;
        }

        if (null === $name) {
            foreach ($this->getRegexes() as $regex) {
                $matches = $this->matchUserAgent($regex['regex']);

                if ($matches) {
                    $name = $this->buildByMatch($regex['name'], $matches);

                    break;
                }
            }
        }

        if (null !== $name) {
            $customBrands = ['HUAWEI HUAWEI', 'viv-vivo', 'SAMSUNG', 'YUHO', 'ZTE', 'HUAWEI'];
            $replaceBrand = $this->replaceBrand ? \array_values(AbstractDeviceParser::$deviceBrands) : [];
            $brands       = \array_merge($customBrands, $replaceBrand);
            $replaceRegex = \sprintf(
                '~(?:^|[^A-Z0-9-_]|[^A-Z0-9-]_|sprd-)(%s)[ _]~is',
                \implode('|', $brands)
            );
            $name         = \preg_replace($replaceRegex, '', $name);
        }

        return \compact('name');
    }

    /**
     * @param bool $stage
     *
     * @return void
     */
    public function setReplaceBrand(bool $stage): void
    {
        $this->replaceBrand = $stage;
    }

    /**
     * Restore UserAgent for suitable condition
     * @param string $userAgent
     *
     * @return string
     */
    protected function restoreUserAgent(string $userAgent): string
    {
        $userAgent    = \rawurldecode($userAgent);
        $regexIphone  = '~ip(?:ad|hone): build/~i';
        $regexAndroid = '~android ~i';

        if (\preg_match($regexIphone, $userAgent) && \preg_match($regexAndroid, $userAgent)) {
            $replaceUserAgent = \preg_replace('~;ip(?:ad|hone):~i', '', $userAgent);

            if ($replaceUserAgent) {
                return $replaceUserAgent;
            }
        }

        return $userAgent;
    }

    /**
     * Overwrite base regex
     * @param string $regex
     *
     * @return string
     */
    protected function createUserAgentRegex(string $regex): string
    {
        $regex = \preg_replace('/\//', '\\/', $regex);
        $regex = \preg_replace('/\+\+/', '+', $regex);

        return \sprintf('~(?:%s)~i', $regex);
    }
}
