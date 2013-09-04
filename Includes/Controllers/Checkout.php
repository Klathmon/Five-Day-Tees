<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/2/13
 */

$settings        = new Settings($database, $config);
$itemMapper      = new \Mapper\Item($database, $config);
$cartItemMapper  = new \Mapper\CartItem($settings, $database);
$expressCheckout = new \PayPal\ExpressCheckout($config);


switch ($request->get(1)) {
    case 'Success':
        //Get Details here or should i wait for the information from the IPN?
        $expressCheckout->addParameter('METHOD', 'DoExpressCheckoutPayment');
        $query = $request->getQueryString();
        \Debug::dump($_SERVER, $expressCheckout);
        die();
        break;
    case 'Cancel':
        break;
    default:
        //Just a plain old checkout, forward the user to PayPal to complete the transaction
        $baseURL = $config->getProtocol() . "//" . $_SERVER['HTTP_HOST'];


        $expressCheckout->addParameter('METHOD', 'SetExpressCheckout');
        $expressCheckout->addParameter('PAYMENTREQUEST_0_PAYMENTACTION', 'Sale');
        $expressCheckout->addParameter('RETURNURL', $baseURL . '/Checkout/Success');
        $expressCheckout->addParameter('CANCELURL', $baseURL . '/Checkout/Cancel');

        $index = 0;
        foreach ($cartItemMapper->listAll() as $cartItem) {
            /** @var \Entity\CartItem $cartItem */
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NAME' . $index, $cartItem->item->getName());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_DESC' . $index, $cartItem->item->getDescription());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_QTY' . $index, $cartItem->getQuantity());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_AMT' . $index, number_format($cartItem->getCurrentPrice(), 2));
            $index++;
        }

        $coupon = $cartItemMapper->getCoupon();

        if (!is_null($coupon)) {
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NAME' . $index, $coupon->getCode());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_DESC' . $index, '');
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_QTY' . $index, 1);
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_AMT' . $index, number_format($cartItemMapper->getCouponDiscount(), 2));
        }

        $expressCheckout->addParameter('PAYMENTREQUEST_0_AMT', number_format($cartItemMapper->getSubtotal(), 2));
        $expressCheckout->addParameter('PAYMENTREQUEST_0_ITEMAMT', number_format($cartItemMapper->getSubtotal(), 2));

        try{
            header('Location: ' . $expressCheckout->getUserCheckoutURL());
        } catch(Exception $e){
            //Something went wrong, show the user an error...
            header('Location: /500');
        }
        break;
}