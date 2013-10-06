<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings = new Settings($database, $config);

$factory = new \DisplayItem\Factory($database, $settings);

$entities = $factory->getStoreFromDatabase();

Debug::dump($entities);


//$factory->persistToDatabase($entity);