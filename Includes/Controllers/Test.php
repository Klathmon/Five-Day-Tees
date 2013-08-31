<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';
$spreadshirtItems = new SpreadshirtItems($database, $config);
$spreadshirtItems->getNewItems();

$couponsMapper = new \Mapper\Coupon($database);

$coupon = $couponsMapper->getByCode('IDIOT');

Debug::dump($coupon);