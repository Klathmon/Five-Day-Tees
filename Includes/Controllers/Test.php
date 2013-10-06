<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!<br/>';

$settings = new Settings($database, $config);

$factory = new \OrderItem\Factory($database, $settings);

$testArray = array (
    'TOKEN' => 'EC-06E7001751479024K',
    'CHECKOUTSTATUS' => 'PaymentActionNotInitiated',
    'TIMESTAMP' => '2013-10-06T22:07:48Z',
    'CORRELATIONID' => '2af60a66dc555',
    'ACK' => 'Success',
    'VERSION' => '74.0',
    'BUILD' => '8006522',
    'EMAIL' => 'imabuyer@fivedaytees.com',
    'PAYERID' => 'RGY4V6HMCLNVC',
    'PAYERSTATUS' => 'verified',
    'FIRSTNAME' => 'Johnny',
    'LASTNAME' => 'LovesShirts',
    'COUNTRYCODE' => 'US',
    'SHIPTONAME' => 'Johnny LovesShirts',
    'SHIPTOSTREET' => '1 Main St',
    'SHIPTOCITY' => 'San Jose',
    'SHIPTOSTATE' => 'CA',
    'SHIPTOZIP' => '95131',
    'SHIPTOCOUNTRYCODE' => 'US',
    'SHIPTOCOUNTRYNAME' => 'United States',
    'ADDRESSSTATUS' => 'Confirmed',
    'CURRENCYCODE' => 'USD',
    'AMT' => '21.50',
    'ITEMAMT' => '17.00',
    'SHIPPINGAMT' => '4.50',
    'HANDLINGAMT' => '0.00',
    'TAXAMT' => '0.00',
    'INSURANCEAMT' => '0.00',
    'SHIPDISCAMT' => '0.00',
    'L_NAME0' => 'Ewoking Dead',
    'L_NUMBER0' => 'SywqyUzOSfV0sTU2USsoyk8pTS4BcgwNLSzNzc0M1Iozq1JtfQE=',
    'L_QTY0' => '1',
    'L_TAXAMT0' => '0.00',
    'L_AMT0' => '17.00',
    'L_ITEMWEIGHTVALUE0' => '   0.00000',
    'L_ITEMLENGTHVALUE0' => '   0.00000',
    'L_ITEMWIDTHVALUE0' => '   0.00000',
    'L_ITEMHEIGHTVALUE0' => '   0.00000',
    'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
    'PAYMENTREQUEST_0_AMT' => '21.50',
    'PAYMENTREQUEST_0_ITEMAMT' => '17.00',
    'PAYMENTREQUEST_0_SHIPPINGAMT' => '4.50',
    'PAYMENTREQUEST_0_HANDLINGAMT' => '0.00',
    'PAYMENTREQUEST_0_TAXAMT' => '0.00',
    'PAYMENTREQUEST_0_INSURANCEAMT' => '0.00',
    'PAYMENTREQUEST_0_SHIPDISCAMT' => '0.00',
    'PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED' => 'false',
    'PAYMENTREQUEST_0_SHIPTONAME' => 'Johnny LovesShirts',
    'PAYMENTREQUEST_0_SHIPTOSTREET' => '1 Main St',
    'PAYMENTREQUEST_0_SHIPTOCITY' => 'San Jose',
    'PAYMENTREQUEST_0_SHIPTOSTATE' => 'CA',
    'PAYMENTREQUEST_0_SHIPTOZIP' => '95131',
    'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'US',
    'PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME' => 'United States',
    'PAYMENTREQUEST_0_ADDRESSSTATUS' => 'Confirmed',
    'L_PAYMENTREQUEST_0_NAME0' => 'Ewoking Dead',
    'L_PAYMENTREQUEST_0_NUMBER0' => 'SywqyUzOSfV0sTU2USsoyk8pTS4BcgwNLSzNzc0M1Iozq1JtfQE=',
    'L_PAYMENTREQUEST_0_QTY0' => '1',
    'L_PAYMENTREQUEST_0_TAXAMT0' => '0.00',
    'L_PAYMENTREQUEST_0_AMT0' => '17.00',
    'L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE0' => '   0.00000',
    'L_PAYMENTREQUEST_0_ITEMLENGTHVALUE0' => '   0.00000',
    'L_PAYMENTREQUEST_0_ITEMWIDTHVALUE0' => '   0.00000',
    'L_PAYMENTREQUEST_0_ITEMHEIGHTVALUE0' => '   0.00000',
    'PAYMENTREQUESTINFO_0_ERRORCODE' => '0',
);

$entity = $factory->createFromPaypal($testArray, '7');

Debug::dump($entity);
