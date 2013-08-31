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
    $ID       = (!empty($_POST['ID']) ? Sanitize::cleanInteger($_POST['ID']) : null);
    $size     = (!empty($_POST['size']) ? Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters) : null);
    $cartItem = (!empty($_POST['ID']) && !empty($_POST['size']) ? $cartItemMapper->getByID($ID . $size) : null);
    switch ($_POST['Command']) {
        case 'AddItem':
            if ($cartItem === false) {
                //There are none of these in the cart yet, so create and add one now...
                $item     = $itemMapper->getByID($ID);
                $cartItem = new \Entity\CartItem($settings, $item, $size);
            } else {
                //There is one of these in the cart already, add one to the Quantity...
                $cartItem->addOneItem();
            }
            $cartItemMapper->persist($cartItem);
            break;
        case 'DeleteItem':
            $cartItemMapper->delete($cartItem);
            break;
        case 'UpdateItem':
            $newSize = Sanitize::preserveGivenCharacters($_POST['NewSize'], $sizeSanitizeCharacters);
            $cartItem->setSize($newSize);
            $cartItemMapper->persist($cartItem);
            break;
        case 'EmptyCart':
            $cartItemMapper->emptyCart();
            break;
    }
}

$template = new FDTSmarty($config, 'Cart.tpl');

$template->assign('cartItems', $cartItemMapper->listAll());
$template->assign('subTotal', '$' . (string)number_format($cartItemMapper->getSubtotal(), 2));
$template->assign('callOutBoxText', $settings->getCartCallout());

$template->output();