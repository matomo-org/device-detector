<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Client\Browser\Engine;

use DeviceDetector\Parser\Client\AbstractClientParser;

/**
 * Class Version
 *
 * Client parser for browser engine version detection
 */
class Version extends AbstractClientParser
{
    /**
     * @var string
     */
    private $engine;

    /**
     * Version constructor.
     *
     * @param string $ua
     * @param string $engine
     */
    public function __construct(string $ua, string $engine)
    {
        parent::__construct($ua);

        $this->engine = $engine;
    }

    /**
     * @inheritdoc
     */
    public function parse(): ?array
    {
        if (empty($this->engine)) {
            return null;
        }

        if ('Gecko' === $this->engine) {
            $pattern = '~[ ](?:rv[: ]([0-9\.]+)).*gecko/[0-9]{8,10}~i';

            if (\preg_match($pattern, $this->userAgent, $matches)) {
                return ['version' => \array_pop($matches)];
            }
        }

        \preg_match(
            "~{$this->engine}\s*/?\s*((?(?=\d+\.\d)\d+[.\d]*|\d{1,7}(?=(?:\D|$))))~i",
            $this->userAgent,
            $matches
        );

        if (!$matches) {
            return null;
        }

        return ['version' => \array_pop($matches)];
    }
}
