<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/22/13
 */

class RequestParser
{
    private $requestURI;
    private $queryString;
    private $array = [];

    public function __construct($requestURI)
    {
        $this->requestURI = $requestURI;

        $this->requestURI = ltrim(
            str_replace('/index.php', '', $this->requestURI),
            '/'
        ); //Remove index.php if it's present (and trim the '/' at the start)
        $URIArray         = explode('?', $this->requestURI);

        $this->array = explode('/', $URIArray[0]);

        if (array_key_exists(1, $URIArray)) {
            $this->queryString = $URIArray[1];
        } else {
            $this->queryString = null;
        }
    }

    public function get($position = null)
    {
        if ($position === null) {
            return $this->array;
        } else {
            return (array_key_exists($position, $this->array) ? $this->array[$position] : null);
        }
    }

    public function getFullURI()
    {
        return $this->requestURI;
    }

    public function getQueryString()
    {
        return $this->queryString;
    }
}