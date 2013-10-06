<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!<br/>';

$settings = new Settings($database, $config);

$factory = new \DisplayItem\Factory($database, $settings);

//$entities = $factory->getStoreFromDatabase();

//Debug::dump($entities);

$array = ['articleID' => '1231153', 'productID' => 'whut!'];
$first = base64_encode(gzdeflate(http_build_query($array), 9));
parse_str(gzinflate(base64_decode($first)), $second);

echo $first . "<br/>";
Debug::dump($second);


//$factory->persistToDatabase($entity);