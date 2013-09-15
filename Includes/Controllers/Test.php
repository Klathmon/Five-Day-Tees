<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings         = new Settings($database, $config);
$salesItemFactory = new \Factory\SalesItem($database, $settings);
$orderItemFactory = new \Factory\OrderItem($database, $settings);
$shoppingCart = new ShoppingCart($database, $settings);

//$shoppingCart->addSalesItem('11897256', 'M');

//$salesitemArray = $shoppingCart->getAllSalesItems();

//Debug::dump($salesitemArray);

$salesItemArray = $salesItemFactory->create('11897256', 'M', 1);

$array = $salesItemFactory->convertArrayToObject($salesItemArray);

Debug::dump($array);