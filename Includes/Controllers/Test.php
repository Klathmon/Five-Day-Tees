<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!<br/>';

$settings = new Settings($database, $config);

$factory = new \Address\Factory($database);

$entity = $factory->createFromPaypal([
        'PAYMENTREQUEST_0_SHIPTOSTREET' => '123 FakeStreet',
        'PAYMENTREQUEST_0_SHIPTOCITY' => 'Garnet Hill',
        'PAYMENTREQUEST_0_SHIPTOSTATE' => 'ZA',
        'PAYMENTREQUEST_0_SHIPTOZIP' => '19065',
        'PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME' => 'United States',
    ]);

Debug::dump($entity);
