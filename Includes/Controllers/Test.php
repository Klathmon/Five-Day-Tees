<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$customerFactory = new \Factory\Customer($database);

$customer = $customerFactory->getByID('1');



Debug::dump($customer);