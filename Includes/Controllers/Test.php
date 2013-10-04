<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings = new Settings($database, $config);

$factory = new \ShippingMethod\Factory($database);

$entities = $factory->getAllFromDatabase();

Debug::dump($entities);


//$factory->persistToDatabase($entity);