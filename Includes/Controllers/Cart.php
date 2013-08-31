<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

$sizeSanitizeCharacters = 'ALMNSX';

$settings       = new Settings($database, $config);
$itemMapper     = new \Mapper\Item($database, $config);
$cartItemMapper = new \Mapper\CartItem($settings);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['Command']) {
        case 'AddItem':
            $ID       = Sanitize::cleanInteger($_POST['ID']);
            $size     = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            $cartItem = $cartItemMapper->getByID($ID . $size);

            if ($cartItem === false) {
                //There are none of these in the cart yet, so create and add one now...
                $item         = $itemMapper->getByID($ID);
                $currentPrice = $settings->getItemCurrentPrice($item);
                $cartItem     = new \Entity\CartItem($item, $size, $currentPrice);
            } else {
                //There is one of these in the cart already, add one to the Quantity...
                $cartItem->addOneItem();
            }

            $cartItemMapper->persist($cartItem);
            break;
        case 'RemoveItem':
            $ID       = Sanitize::cleanInteger($_POST['ID']);
            $size     = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            $cartItem = $cartItemMapper->getByID($ID . $size);

            $cartItemMapper->delete($cartItem);
            break;
        case 'UpdateItem':
            $ID       = Sanitize::cleanInteger($_POST['ID']);
            $size     = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            $quantity = Sanitize::cleanInteger($_POST['Quantity']);
            $cartItem = $cartItemMapper->getByID($ID . $size);

            if ($quantity == 0) {
                //Set to 0, so delete the item from the cart
                $cartItemMapper->delete($cartItem);
            } else {
                //Update the item
                $cartItem->setQuantity($quantity);
                $cartItemMapper->persist($cartItem);
            }
            break;
        case 'EmptyCart':
            $cartItemMapper->emptyCart();
            break;
    }

    echo '$' . number_format($cartItemMapper->getSubtotal(), 2);
} else {
    $template = new FDTSmarty($config, 'Cart.tpl');

    $template->assign('cartItems', $cartItemMapper->listAll());
    $template->assign('subTotal', '$' . (string)number_format($cartItemMapper->getSubtotal(), 2));
    $template->assign('callOutBoxText', $settings->getCartCallout());
    $template->assign('disableCoupon', false);

    $template->output();
}