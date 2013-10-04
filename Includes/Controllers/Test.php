<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings = new Settings($database, $config);

$factory = new \Item\Factory($database, $settings);


$items = $factory->getFeaturedFromDatabase();

Debug::dump($items);


//$factory->persistToDatabase($entity);