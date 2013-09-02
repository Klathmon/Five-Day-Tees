<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/2/13
 */

$settings       = new Settings($database, $config);
$itemMapper     = new \Mapper\Item($database, $config);
$cartItemMapper = new \Mapper\CartItem($settings, $database);

$parameters = [
    'RETURNURL'                => '/Checkout/Success',
    'CANCELURL'                => '/Checkout/Cancel',
    'PAYMENTREQUEST_0_AMT'     => $cartItemMapper->getSubtotal(),
    'PAYMENTREQUEST_0_ITEMAMT' => $cartItemMapper->getSubtotal()
];

