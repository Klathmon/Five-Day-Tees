<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/22/13
 */

class ConfigParser
{
    private $configVariables = [];

    public function __construct($configFile)
    {
        if (is_file($configFile)) {
            $handle = fopen($configFile, 'r');

            while (!feof($handle)) {
                $line = trim(fgets($handle));

                if ($line[0] != '#' && $line != '') {
                    list($name, $value) = explode('=', $line);

                    $this->configVariables[strtoupper(trim($name))] = trim($value);
                }
            }

            fclose($handle);
        } else {
            throw new Exception('Config file not found!');
        }
    }

    public function __call($funcName, $arguments)
    {
        $varName = strtoupper(substr($funcName, 4));

        return $this->configVariables[$varName];
    }

    public function getMode()
    {
        return $this->configVariables['MODE'];
    }

    public function getDatabaseHost()
    {
        return $this->configVariables['DATABASE_HOST'];
    }

    public function getDatabaseName()
    {
        return $this->configVariables['DATABASE_NAME'];
    }

    public function getDatabaseUsername()
    {
        return $this->configVariables['DATABASE_USERNAME'];
    }

    public function getDatabasePassword()
    {
        return $this->configVariables['DATABASE_PASSWORD'];
    }

    public function getSpreadAPIURL()
    {
        return $this->configVariables['SPREAD_API_URL'];
    }

    public function getSpreadShopID()
    {
        return $this->configVariables['SPREAD_SHOP_ID'];
    }

    public function getBaseDirectory()
    {
        return $this->configVariables['BASE_DIR'];
    }

    public function getStaticURL()
    {
        return $this->configVariables['STATIC_URL'];
    }

    public function getSiteName()
    {
        return $this->configVariables['SITE_NAME'];
    }

    public function getGoogleAnalytics()
    {
        return $this->configVariables['GOOGLE_ANALYTICS'];
    }
}