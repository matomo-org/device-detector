<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Parser\Client\Browser\Engine;

use DeviceDetector\Parser\Client\ClientParserAbstract;

/**
 * Class Version
 *
 * Client parser for browser engine version detection
 *
 * @package DeviceDetector\Parser\Client\Browser\Engine
 */
class Version extends ClientParserAbstract
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
    public function __construct($ua, $engine)
    {
        parent::__construct($ua);

        $this->engine = $engine;
    }

    public function parse()
    {
        if (empty($this->engine)) {
            return '';
        }

        preg_match("~$this->engine\s*/?\s*((?(?=\d+\.\d)\d+[.\d]*|\d{1,7}(?=(?:\D|$))))~i", $this->userAgent, $matches);

        if (!$matches) {
            return '';
        }

        return array_pop($matches);
    }
}
