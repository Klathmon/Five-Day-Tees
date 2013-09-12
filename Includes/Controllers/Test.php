<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings = new Settings($database, $config);

$salesItemFactory = new \Factory\SalesItem($database, $settings);

$salesItemFactory->create('59', 'XL', 1);