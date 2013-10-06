<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

//TODO: Start here, get the viewport and main.js to send the correct information to the Cart.php file and refactor that so it works with the new Cart Model


$sizeSanitizeCharacters = 'ALMNSX';

$settings              = new Settings($database, $config);
$shippingMethodFactory = new \ShippingMethod\Factory($database);
$shoppingCart          = new ShoppingCart($database, $settings);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    switch ($_POST['Command']) {
        case 'AddItem':
            $size   = Sanitize::preserveGivenCharacters($_POST['size'], $sizeSanitizeCharacters);
            $shoppingCart->addCartItemByItemIDAndSize($_POST['itemID'], $size);
            break;
        case 'RemoveItem':
            $shoppingCart->deleteCartItem($_POST['ID']);
            break;
        case 'UpdateItem':
            $quantity = Sanitize::cleanInteger($_POST['Quantity']);

            try{
                $cartItem = $shoppingCart->getCartItemByID($_POST['ID']);
                $cartItem->setQuantity($quantity);
            } catch(Exception $e){
                //Silently skip if someone tries to update an item that's not in their cart, or something else goes wrong.
            }
            break;
        case 'EmptyCart':
            $shoppingCart->emptyCart();
            break;
        case 'AddCouponToCart':
            $couponCode = Sanitize::cleanAlphaNumeric($_POST['Code']);
            try{
                $couponFactory = new \Coupon\Factory($database);
                $coupon        = $couponFactory->getByCodeFromDatabase($couponCode);
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
                $shippingMethod = $shippingMethodFactory->getByIDFromDatabase($ID);
                $shoppingCart->setShippingMethod($shippingMethod);
            } catch(Exception $e){
                //Silently skip if someone tries to add a shipping method that doesn't exist
            }
            break;
    }
}

if ($shoppingCart->getPreShippingTotal()->getDecimal() >= 150) {
    $shoppingCart->emptyCart();
    echo "<script>alert('The you have too many items in your cart! For orders over $150 please contact us directly!');</script>";
}

$template = new FDTSmarty($config, 'Cart.tpl', 'Cart');

$template->assign('cartItems', $shoppingCart->getCartItems());
$template->assign('shippingMethods', $shippingMethodFactory->getAllEnabledFromDatabase());
$template->assign('chosenShippingMethod', $shoppingCart->getShippingMethod());
$template->assign('coupon', $shoppingCart->getCoupon());
$template->assign('preShippingTotal', $shoppingCart->getPreShippingTotal());
$template->assign('total', $shoppingCart->getFinalTotal());
$template->assign('callOutBoxText', $settings->getCartCallout());

$template->output();