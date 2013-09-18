<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/22/13
 */

class ConfigParser
{
    private $configArray = [];

    public function __construct($configFile)
    {
        $file = new SplFileObject($configFile, 'r');

        $currentSection = null;
        
        foreach($file as $lineNumber => $line){
            if($line[0] === ' ' || $line[0] === '#' || $line[0] === ';' || $line[0] === "\n"){
                //Skip this line
            }else{
                if($line[0] === '['){
                    //It's a section, so set the current section
                    $currentSection = strtoupper(trim($line, " \t\n\r\0[]"));
                }else{
                    //It's a line with stuff in it
                    list($name, $value) = explode('=', $line, 2);
                    $name = strtoupper(trim($name));
                    $value = trim($value);
                    
                    if(strtoupper($value) === 'TRUE'){
                        $value = true;
                    }elseif(strtoupper($value) === 'FALSE'){
                        $value = false;
                    }
                    
                    if(is_null($currentSection)){
                        $this->configArray[$name] = $value;
                    }else{
                        $this->configArray[$currentSection][$name] = $value;
                    }
                }
            }
        }
        
        var_dump($this->configArray);
    }
    
    public function get($section, $key = null)
    {
        if(is_null($key)){
            return $this->configArray[strtoupper($section)];
        }else{
            return $this->configArray[strtoupper($section)][strtoupper($key)];
        }
    }

    public function getProtocol()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            return 'https:';
        } else {
            return 'http:';
        }
    }

    public function getBaseDirectory()
    {
        return getcwd() . '/';
    }
}