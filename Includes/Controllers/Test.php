<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings = new Settings($database, $config);

$factory = new \Article\Factory($database, $settings);


$array['name']            = 'Test Shirt';
$array['date']            = (new DateTime('now'))->format('Y-m-d');
$array['articleImageURL'] = 'http://lolwhut.com';
$array['salesLimit']      = 100;
$array['votes']           = 0;



$entity = $factory->createFromData($array);

Debug::dump($entity);


//$factory->persistToDatabase($entity);