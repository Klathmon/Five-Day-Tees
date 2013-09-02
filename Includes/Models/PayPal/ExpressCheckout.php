<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/2/13
 */

namespace PayPal;

use ConfigParser, Settings, Exception;

class ExpressCheckout
{
    private $config;

    private $settings;

    private $parameters = [];


    public function __construct(ConfigParser $config, Settings $settings)
    {
        $this->config   = $config;
        $this->settings = $settings;

        $this->parameters['VERSION']   = $this->config->getPayPalAPIVersion();
        $this->parameters['USER']      = $this->config->getPayPalAPIUsername();
        $this->parameters['PWD']       = $this->config->getPayPalAPIPassword();
        $this->parameters['SIGNATURE'] = $this->config->getPayPalAPISignature();
    }

    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function executeRequest()
    {
        $curl = curl_init();

        $payload = http_build_query($this->parameters);

        $curlOptions = [
            CURLOPT_URL            => $this->config->getPayPalAPIEndpoint(),
            CURLOPT_VERBOSE        => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO         => $this->config->getBaseDirectory() . 'Config/cacert.pem', //CA cert file
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $payload
        ];

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new Exception($error);
        }

        curl_close($curl);

        $responseArray = [];

        parse_str($response, $responseArray);

        return $responseArray;
    }
}