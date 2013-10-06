<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!<br/>';

$settings = new Settings($database, $config);

$factory = new \Customer\Factory($database);

$entity = $factory->createFromPaypal([
        'PAYERID' => '123456789',
        'FIRSTNAME' => 'Gregory',
        'LASTNAME' => 'Benner',
        'EMAIL' => 'gregbenner1@gmail.com',
    ]);

Debug::dump($entity);
