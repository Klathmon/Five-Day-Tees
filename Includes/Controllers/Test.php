<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings         = new Settings($database, $config);

$factory = new \Factory\Item($database, $settings);

$item = $factory->getAll(true);

var_dump($item);