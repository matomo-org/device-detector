<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Yaml;

use \Spyc AS SpycParser;

class Spyc implements Parser
{
    public function parseFile($file)
    {
        return SpycParser::YAMLLoad($file);
    }
}
