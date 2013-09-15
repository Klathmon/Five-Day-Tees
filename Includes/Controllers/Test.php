<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$addressFactory = new \Factory\Address($database);

$address = $addressFactory->create(null, '123 Fake Street', null, 'FakesBurg', 'PA', '19060', 'US');

$addressFactory->persist($address);
Debug::dump($address);