<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/2/13
 */

$settings     = new Settings($database, $config);
$itemMapper   = new \Mapper\Item($database, $config);
$shoppingCart = new ShoppingCart($settings);


switch ($request->get(1)) {
    case 'Success':
        $query = $request->getQueryArray();

        $orderDetails = new \PayPal\ExpressCheckout($config);
        $orderDetails->addParameter('METHOD', 'GetExpressCheckoutDetails');
        $orderDetails->addParameter('TOKEN', $query['token']);
        $orderDetails->addParameter('PAYERID', $query['PayerID']);
        $orderDetails->getCheckoutDetails();

        Debug::dump($orderDetails);
        //TODO: Enter all the information from GetExpressCheckoutDetails into the database as a "NEW" order.

        die();


        $expressCheckoutPayment = new \PayPal\ExpressCheckout($config);
        $expressCheckoutPayment->addParameter('LOCALECODE', 'US');
        $expressCheckoutPayment->addParameter('CURRENCYCODE', 'USD');
        $expressCheckoutPayment->addParameter('PAYMENTACTION', 'Sale');
        $expressCheckoutPayment->addParameter('TOKEN', $query['token']);
        $expressCheckoutPayment->addParameter('PAYERID', $query['PayerID']);
        $expressCheckoutPayment->addParameter('METHOD', 'DoExpressCheckoutPayment');

        try{
            $expressCheckoutPayment->getUserCheckoutURL();
        } catch(Exception $e){
            //Shit hit the fan, tell the customer that their order is not going to work...
            Debug::dump($expressCheckoutPayment);
        }

        echo 'IT WORKED!';


        die();
        break;
    case 'Cancel':
        header('Location: /');
        break;
    default:
        //Just a plain old checkout, forward the user to PayPal to complete the transaction
        $baseURL = $config->getProtocol() . "//" . $_SERVER['HTTP_HOST'];

        $expressCheckout = new \PayPal\ExpressCheckout($config);
        $expressCheckout->addParameter('LOCALECODE', 'US');
        $expressCheckout->addParameter('CURRENCYCODE', 'USD');
        $expressCheckout->addParameter('PAYMENTACTION', 'Sale');
        $expressCheckout->addParameter('REQCONFIRMSHIPPING', '0'); //Set this to 1 to always require a confirmed address...
        $expressCheckout->addParameter('RETURNURL', $baseURL . '/Checkout/Success');
        $expressCheckout->addParameter('CANCELURL', $baseURL . '/Checkout/Cancel');

        //$expressCheckout->addParameter('PAGESTYLE', 'Page Style Name Goes Here');
        //$expressCheckout->addParameter('HDRIMG', 'Full path to header img here (can be up to 750px x 90px');
        //$expressCheckout->addParameter('HDRBACKCOLOR', 'Hex background color here (for the header)');
        //$expressCheckout->addParameter('HDRBORDERCOLOR', 'Hex border color here (for the header)');

        $index = 0;
        foreach ($shoppingCart->getCartItems() as $cartItem) {
            $itemNumber  = $cartItem->item->getID() . "|" . $cartItem->getSize();
            $description = $cartItem->item->getDescription();

            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NAME' . $index, $cartItem->item->getName());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NUMBER' . $index, $itemNumber);
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_DESC' . $index, $description);
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_QTY' . $index, $cartItem->getQuantity());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_AMT' . $index, number_format($cartItem->getCurrentPrice(), 2));
            $index++;
        }

        try{
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NAME' . $index, $shoppingCart->getCoupon()->getCode());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_DESC' . $index, '');
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_QTY' . $index, 1);
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_AMT' . $index, number_format($shoppingCart->getCouponDiscount(), 2));
            $index++;
        } catch(Exception $e){
            //Silently skip if there is no coupon
        }

        $subtotal = $shoppingCart->getPreShippingTotal();

        $shippingAmount = $shoppingCart->getShippingMethod()->calculateShippingPrice($subtotal);


        $expressCheckout->addParameter('PAYMENTREQUEST_0_ITEMAMT', number_format($subtotal, 2));
        $expressCheckout->addParameter('PAYMENTREQUEST_0_SHIPPINGAMT', number_format($shippingAmount));
        $expressCheckout->addParameter('PAYMENTREQUEST_0_AMT', number_format($shoppingCart->getFinalTotal(), 2));

        try{
            header('Location: ' . $expressCheckout->getUserCheckoutURL());
        } catch(Exception $e){
            //Something went wrong, show the user an error...
            header('Location: /500');
        }
        break;
}