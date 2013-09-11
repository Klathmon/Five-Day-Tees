<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$shippingMethodFactory = new \Factory\ShippingMethod($database);

$shippingMethods = $shippingMethodFactory->getAll();

var_dump($shippingMethods);