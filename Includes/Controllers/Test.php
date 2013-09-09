<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings = new Settings($database, $config);

$itemFactory = new \Factory\Item($database, $settings);

$items = $itemFactory->getVault();

var_dump($items);