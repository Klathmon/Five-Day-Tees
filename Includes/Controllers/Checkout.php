<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/2/13
 */

$settings     = new Settings($database, $config);
$shoppingCart = new ShoppingCart($database, $settings);

switch ($request->get(1)) {
    case 'Success':
        $query = $request->getQueryArray();

        $orderDetails = new \PayPal\ExpressCheckout($config);
        $orderDetails->addParameter('TOKEN', $query['token']);
        $orderDetails->addParameter('PAYERID', $query['PayerID']);
        $response = $orderDetails->getCheckoutDetails();

        Debug::dump($response); //TODO Remove this!

        $addressFactory = new \Factory\Address($database);

        $address = $addressFactory->fetchOrCreate(
            $response['PAYMENTREQUEST_0_SHIPTOSTREET'],
            null,
            $response['PAYMENTREQUEST_0_SHIPTOCITY'],
            $response['PAYMENTREQUEST_0_SHIPTOSTATE'],
            $response['PAYMENTREQUEST_0_SHIPTOZIP'],
            $response['PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME']
        );

        $customerFactory = new \Factory\Customer($database);

        try{
            $customer = $customerFactory->getByPayPalPayerID($response['PAYERID']);
            $customer->setFirstName($response['FIRSTNAME']);
            $customer->setLastName($response['LASTNAME']);
            $customer->setEmail($response['EMAIL']);
        } catch(Exception $e){
            $customer = $customerFactory->create(
                null,
                $response['PAYERID'],
                $response['FIRSTNAME'],
                $response['LASTNAME'],
                null,
                null,
                $response['EMAIL'],
                1
            );
        }

        $customerFactory->persist($customer);

        for ($x = 0; array_key_exists('L_PAYMENTREQUEST_0_NUMBER' . $x, $response); $x++) {
            $items[] = [
                'ID'       => $response['L_PAYMENTREQUEST_0_NUMBER' . $x],
                'name'     => $response['L_PAYMENTREQUEST_0_NAME' . $x],
                'quantity' => $response['L_PAYMENTREQUEST_0_QTY' . $x],
                'amount'   => $response['L_PAYMENTREQUEST_0_AMT' . $x],
            ];
        }

        var_dump($items);

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
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NAME' . $index, $cartItem->getArticle()->getName());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NUMBER' . $index, $cartItem->getID());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_DESC' . $index, $cartItem->getProduct()->getDescription());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_QTY' . $index, $cartItem->getQuantity());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_AMT' . $index, number_format($cartItem->getCurrentPrice()->getDecimal(), 2));
            $index++;
        }

        /** Add the coupon as an item with a negative price if it exists. */
        $coupon = $shoppingCart->getCoupon();
        if (!is_null($coupon)) {
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NAME' . $index, 'COUPON:' . $coupon->getCode());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_NUMBER' . $index, $coupon->getID());
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_QTY' . $index, 1);
            $expressCheckout->addParameter('L_PAYMENTREQUEST_0_AMT' . $index, number_format($shoppingCart->getCouponAmount()->getDecimal(), 2));
            $index++;
        }

        $subtotal       = $shoppingCart->getPreShippingTotal();
        $shippingAmount = $shoppingCart->getShippingMethod()->calculateShippingPrice($subtotal);
        $finalTotal     = $shoppingCart->getFinalTotal();


        $expressCheckout->addParameter('PAYMENTREQUEST_0_ITEMAMT', number_format($subtotal->getDecimal(), 2));
        $expressCheckout->addParameter('PAYMENTREQUEST_0_SHIPPINGAMT', number_format($shippingAmount->getDecimal(), 2));
        $expressCheckout->addParameter('PAYMENTREQUEST_0_AMT', number_format($finalTotal->getDecimal(), 2));

        try{
            header('Location: ' . $expressCheckout->getUserCheckoutURL());
        } catch(Exception $e){
            //Something went wrong, show me what if i'm debugging, otherwise show the user a 500 error...
            if($config->get('DEBUG', 'DEBUGGING')){
                Debug::dump($e);
            }else{
                header('Location: /500');
            }
        }
        break;
}