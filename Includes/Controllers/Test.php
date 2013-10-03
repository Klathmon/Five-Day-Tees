<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

/*
$settings         = new Settings($database, $config);

$spread = new \SpreadShirt\SpreadShirtItems($database, $config);

$spread->getNewItems();
*/

$cache->smartFetch('key', function(){
    $data = time();
    
    return $data;
}, '1 hour');

$string = (string) PHP_INT_MAX;

$length = strlen(PHP_INT_MAX);

echo "<br/>" . $length . "<br/>";