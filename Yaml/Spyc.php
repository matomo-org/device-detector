<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Yaml;

use \Spyc as SpycParser;

class Spyc implements ParserInterface
{
    /**
     * Parses the file with the given filename using Spyc and returns the converted content
     *
     * @param string $file
     *
     * @return mixed
     */
    public function parseFile(string $file)
    {
        return SpycParser::YAMLLoad($file);
    }
}
