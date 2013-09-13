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
            $array = file($configFile, FILE_SKIP_EMPTY_LINES);

            foreach ($array as $line) {
                if ($line[0] != '#' && trim($line) != '') {
                    list($name, $value) = explode('=', $line, 2);

                    $temp[strtoupper(trim($name))] = trim($value);
                }
            }

            $this->configVariables = $temp;
        } else {
            throw new Exception('Config file not found!');
        }
    }

    public function getMode()
    {
        return $this->configVariables['MODE'];
    }

    public function getProtocol()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            return 'https:';
        } else {
            return 'http:';
        }
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
        return getcwd() . '/';
    }

    public function getSiteName()
    {
        return $this->configVariables['SITE_NAME'];
    }

    public function getGoogleAnalytics()
    {
        return $this->configVariables['GOOGLE_ANALYTICS'];
    }

    public function getPayPalAPIVersion()
    {
        return $this->configVariables['PAYPAL_API_VERSION'];
    }

    public function getPayPalAPIUsername()
    {
        return $this->configVariables['PAYPAL_USERNAME'];
    }

    public function getPayPalAPIPassword()
    {
        return $this->configVariables['PAYPAL_PASSWORD'];
    }

    public function getPayPalAPISignature()
    {
        return $this->configVariables['PAYPAL_SIGNATURE'];
    }

    public function getPayPalAPIEndpoint()
    {
        return $this->configVariables['PAYPAL_ENDPOINT'];
    }

    public function getPayPalExpressCheckoutURL()
    {
        return $this->configVariables['PAYPAL_EXPRESS_CHECKOUT_URL'];
    }

    public function showErrors()
    {
        if (strtoupper($this->configVariables['SHOW_ERRORS']) == 'TRUE') {
            return true;
        } else {
            return false;
        }
    }

    public function debugModeOn()
    {
        if (strtoupper($this->configVariables['DEBUGGING']) == 'TRUE') {
            return true;
        } else {
            return false;
        }
    }

    public function useStaticCaching()
    {
        if (strtoupper($this->configVariables['STATIC_CACHING']) == 'TRUE') {
            return true;
        } else {
            return false;
        }
    }

    public function forceRecompile()
    {
        if (strtoupper($this->configVariables['FORCE_RECOMPILE']) == 'TRUE') {
            return true;
        } else {
            return false;
        }
    }

}