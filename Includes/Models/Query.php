<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/22/13
 */

class Query
{
    private $query = [];

    public function __construct()
    {
        $requestURI  = strtolower($_SERVER['REQUEST_URI']); //Make it lowercase
        $queryString = ltrim(str_replace('/index.php', '', $requestURI), '/'); //Remove index.php if it's present (and trim the '/' at the start)
        $this->query = explode('/', $queryString);
    }

    public function get($position = null)
    {
        if ($position === null) {
            return $this->query;
        } else {
            return $this->query[$position];
        }
    }
}