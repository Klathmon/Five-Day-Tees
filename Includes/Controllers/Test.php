<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!<br/>';

$settings = new Settings($database, $config);

$cart = new \ShoppingCart($database, $settings);

$cart->addCartItemByItemIDAndSize('SywqyUzOSfV0sTU2USsoyk8pTS4BcgwNLSzNzc0MAA==', 'M');

Debug::dump($cart);
