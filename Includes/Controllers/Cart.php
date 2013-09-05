<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

$sizeSanitizeCharacters = 'ALMNSX';

$settings             = new Settings($database, $config);
$itemMapper           = new \Mapper\Item($database, $config);
$couponMapper         = new \Mapper\Coupon($database);
$shoppingCart         = new ShoppingCart($settings);
$shippingMethodMapper = new \Mapper\ShippingMethod($database);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_POST['Command']) {
        case 'AddItem':
            $ID   = Sanitize::cleanInteger($_POST['ID']);
            $size = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            try{
                //Try to get the item from the cart and add one
                $cartItem = $shoppingCart->getByID($ID . $size);
                $cartItem->addOneItem();
            } catch(Exception $e){
                //Failed to get the item from the cart, so create it
                $item         = $itemMapper->getByID($ID);
                $currentPrice = $settings->getItemCurrentPrice($item);
                $cartItem     = new \Entity\CartItem($item, $size, $currentPrice);
            }

            $shoppingCart->persist($cartItem);
            break;
        case 'RemoveItem':
            $ID   = Sanitize::cleanInteger($_POST['ID']);
            $size = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            try{
                $cartItem = $shoppingCart->getByID($ID . $size);
                $shoppingCart->deleteCartItem($cartItem);
            } catch(Exception $e){
                //Silently skip if someone tries to delete an item that's not in their cart
            }
            break;
        case 'UpdateItem':
            $ID       = Sanitize::cleanInteger($_POST['ID']);
            $size     = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            $quantity = Sanitize::cleanInteger($_POST['Quantity']);

            try{
                $cartItem = $shoppingCart->getByID($ID . $size);
                if ($quantity == 0) {
                    //Set to 0, so delete the item from the cart
                    $shoppingCart->deleteCartItem($cartItem);
                } else {
                    //Update the item
                    $cartItem->setQuantity($quantity);
                    $shoppingCart->persist($cartItem);
                }
            } catch(Exception $e){
                //Silently skip if someone tries to update an item that's not in their cart
            }


            break;
        case 'EmptyCart':
            $shoppingCart->emptyCart();
            break;
        case 'AddCouponToCart':
            $couponCode = Sanitize::cleanAlphaNumeric($_POST['Code']);
            $coupon     = $couponMapper->getByCode($couponCode);
            try{
                $shoppingCart->setCoupon($coupon);
            } catch(Exception $e){
                //Silently skip if someone tries to add a coupon that doesn't exist or has no more uses
            }
            break;
        case 'RemoveCouponFromCart':
            $shoppingCart->deleteCoupon();
            break;
        case 'SetShipping':
            $ID = Sanitize::cleanInteger($_POST['ID']);
            try{
                $shippingMethod = $shippingMethodMapper->getByID($ID);
                $shoppingCart->setShippingMethod($shippingMethod);
            } catch(Exception $e){
                //Silently skip if someone tries to add a shipping method that doesn't exist
            }
            break;
    }
}

if ($shoppingCart->getPreShippingTotal() >= 150) {
    $shoppingCart->emptyCart();
    echo "<script>alert('The you have too many items in your cart! For orders over $150 please contact us directly!');</script>";
}

$template = new FDTSmarty($config, 'Cart.tpl');

$template->assign('cartItems', $shoppingCart->getCartItems());
$template->assign('shippingMethods', $shippingMethodMapper->listAllEnabled());
$template->assign('preShippingTotal', $shoppingCart->getPreShippingTotal());
$template->assign('total', '$' . (string)number_format($shoppingCart->getFinalTotal(), 2));
$template->assign('callOutBoxText', $settings->getCartCallout());

try{
    $template->assign('chosenShippingMethodID', $shoppingCart->getShippingMethod()->getID());
} catch(Exception $e){
    $template->assign('chosenShippingMethodID', -1); //This will make it so none of them are selected
}

try{
    $coupon = $shoppingCart->getCoupon();
    $template->assign('disableCoupon', true);
    $template->assign('couponCode', $coupon->getCode());
    $template->assign('isPercent', $coupon->isPercent());
    $template->assign('Amount', (double)$coupon->getAmount() * -1);
} catch(Exception $e){
    $template->assign('disableCoupon', false);
}

$template->output();