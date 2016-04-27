<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Yaml;

use Symfony\Component\Yaml\Parser AS SymfonyParser;

class Symfony implements Parser
{
    public function parseFile($file)
    {
        $parser = new SymfonyParser();
        return $parser->parse(file_get_contents($file));
    }
}
