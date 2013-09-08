<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/2/13
 */

namespace PayPal;

use ConfigParser, Exception, cURL;

class ExpressCheckout
{
    private $config;

    private $curl;

    private $parameters = [];

    private $response;


    public function __construct(ConfigParser $config)
    {
        $this->config = $config;

        try{
            $this->curl = new cURL($this->config->getPayPalAPIEndpoint());
            $this->curl->setOption(CURLOPT_VERBOSE, 1);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, 2);
            $this->curl->setOption(CURLOPT_CAINFO, $this->config->getBaseDirectory() . 'Config/cacert.pem'); //CA Cert File
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, 1);
            $this->curl->setOption(CURLOPT_POST, 1);
        } catch(Exception $e){
            throw new Exception('Error starting cURL');
        }

        $this->addParameter('VERSION', $this->config->getPayPalAPIVersion());
        $this->addParameter('USER', $this->config->getPayPalAPIUsername());
        $this->addParameter('PWD', $this->config->getPayPalAPIPassword());
        $this->addParameter('SIGNATURE', $this->config->getPayPalAPISignature());
    }

    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function getUserCheckoutURL()
    {
        $this->addParameter('METHOD', 'SetExpressCheckout');

        $this->response = $this->executeRequest();


        if (is_array($this->response) && $this->response['ACK'] == 'Success') {

            $parameters = ['cmd' => '_express-checkout', 'token' => $this->response['TOKEN']];

            $url = $this->config->getPayPalExpressCheckoutURL() . '?' . http_build_query($parameters);

        } else {
            throw new Exception('Error Forwarding to PayPal!  ' . $this->response['L_LONGMESSAGE0']);
        }

        return $url;
    }

    public function getCheckoutDetails()
    {

        $this->addParameter('METHOD', 'GetExpressCheckoutDetails');

        $this->response = $this->executeRequest();

        return $this->response;
    }

    public function finalizeOrder()
    {

        $this->addParameter('METHOD', 'DoExpressCheckoutPayment');

        $this->response = $this->executeRequest();

        return $this->response;
    }

    public function getLastResponse()
    {
        return $this->response;
    }

    public function __destruct()
    {
        $this->curl->close();
    }

    private function executeRequest()
    {
        try{
            $this->curl->setOption(CURLOPT_POSTFIELDS, http_build_query($this->parameters));

            $response = $this->curl->execute();
        } catch(Exception $e){
            throw new Exception('Error executing cURL');
        }

        $responseArray = [];

        parse_str($response, $responseArray);

        return $responseArray;
    }
}