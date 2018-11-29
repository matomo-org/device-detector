<?php
namespace DeviceDetector\Yaml;

class Pecl implements Parser
{
    public function parseFile($file)
    {
        if(function_exists(yaml_parse_file) === false)
            throw new \Exception('Pecl YAML extension is not installed');
        
        return yaml_parse_file($file);
    }
}
