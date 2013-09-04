<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/4/13
 */


class cURL
{
    private $resource;

    public function __construct($url = null)
    {
        $this->resource = curl_init();

        $this->setOption(CURLOPT_USERAGENT, 'FiveDayTees_cURL');

        if (!is_null($url)) {
            $this->setOption(CURLOPT_URL, $url);
        }
    }

    public function setOption($name, $value)
    {
        if (!curl_setopt($this->resource, $name, $value)) {
            throw new Exception('Error setting cURL option');
        }
    }

    public function execute()
    {
        $response = curl_exec($this->resource);

        if (curl_errno($this->resource)) {

            $error = curl_error($this->resource);

            throw new Exception('Error executing cURL, ' . $error);
        }

        return $response;
    }

    public function close()
    {
        curl_close($this->resource);
    }
}