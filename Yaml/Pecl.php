<?php
/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
namespace DeviceDetector\Yaml;

use Exception;

/**
 * Class Pecl
 * 
 * Parses a YAML file with LibYAML library
 * 
 * @package DeviceDetector\Yaml
 * @see http://php.net/manual/en/function.yaml-parse-file.php
 */
class Pecl implements Parser
{
    /**
     * @param string $file The path to the YAML file to be parsed
     * 
     * @return mixed The YAML converted to a PHP value or FALSE on failure
     * @throws Exception If the YAML extension is not installed
     */
    public function parseFile($file)
    {
        if(function_exists('yaml_parse_file') === false)
        {
            throw new Exception('Pecl YAML extension is not installed');
        }
        
        return yaml_parse_file($file);
    }
}
