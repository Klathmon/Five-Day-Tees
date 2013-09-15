<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

//TODO: Start here, get the viewport and main.js to send the correct information to the Cart.php file and refactor that so it works with the new Cart Model


$sizeSanitizeCharacters = 'ALMNSX';

$settings              = new Settings($database, $config);
$shoppingCart          = new ShoppingCart($database, $settings);
$shippingMethodFactory = new \Factory\ShippingMethod($database);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    switch ($_POST['Command']) {
        case 'AddItem':
            $ID   = Sanitize::cleanInteger($_POST['ID']);
            $size = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            $shoppingCart->addSalesItem($ID, $size);
            break;
        case 'RemoveItem':
            $ID   = Sanitize::cleanInteger($_POST['ID']);
            $size = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            $shoppingCart->deleteSalesItem($ID, $size);
            break;
        case 'UpdateItem':
            $ID       = Sanitize::cleanInteger($_POST['ID']);
            $size     = Sanitize::preserveGivenCharacters($_POST['Size'], $sizeSanitizeCharacters);
            $quantity = Sanitize::cleanInteger($_POST['Quantity']);

            try{
                $salesItem = $shoppingCart->getSalesItem($ID, $size);
                $salesItem->setQuantity($quantity);
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
                $shoppingCart->setCoupon($couponCode);
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
                $shoppingCart->setShippingMethod($ID);
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

$template = new FDTSmarty($config, 'Cart.tpl');

foreach ($shoppingCart->getAllSalesItems() as $salesItem) {
    $salesItems[] = [
        'articleID' => $salesItem->getArticle()->getID(),
        'size' => $salesItem->getSize(),
        'name' => $salesItem->getName(),
        'type' => $salesItem->getProduct()->getType(),
        'price' => $salesItem->getPurchasePrice()->getNiceFormat(),
        'quantity' => $salesItem->getQuantity()
    ];
}

foreach($shippingMethodFactory->getAll(false) as $shippingMethod){
    $shippingMethods[] = [
        'ID' => $shippingMethod->getID(),
        'name' => $shippingMethod->getName(),
        'price' => $shippingMethod->calculateShippingPrice($shoppingCart->getPreShippingTotal())->getNiceFormat()
    ];
}

$template->assign('salesItems', $salesItems);
$template->assign('shippingMethods', $shippingMethods);
$template->assign('total', $shoppingCart->getFinalTotal()->getNiceFormat());
$template->assign('callOutBoxText', $settings->getCartCallout());

try{
    $template->assign('chosenShippingMethodID', $shoppingCart->getShippingMethod()->getID());
} catch(Exception $e){
    $template->assign('chosenShippingMethodID', -1); //This will make it so none of them are selected
}

try{
    $coupon = $shoppingCart->getCoupon();
    $template->assign('couponCode', $coupon->getCode());
    $template->assign('amount', $coupon->getAmount()->getNiceFormat());
} catch(Exception $e){
    //Do nothing!
}

$template->output();