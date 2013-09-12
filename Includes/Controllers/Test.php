<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings = new Settings($database, $config);

$salesItemFactory = new \Factory\SalesItem($database, $settings);

$salesItem = $salesItemFactory->create(12101407, 'XL', 1);

var_dump($salesItem);