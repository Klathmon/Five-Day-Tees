<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$settings         = new Settings($database, $config);
$orderItemFactory = new \Factory\OrderItem($database, $settings);

$designArray  = array(
    'ID'             => '68',
    'name'           => 'Hookah Kitty',
    'displayDate'    => '2013-09-08',
    'designImageURL' => '//image.spreadshirt.com/image-server/v1/compositions/104563319/views/1',
    'salesLimit'     => '100',
    'votes'          => '0',
);
$articleArray = array(
    'ID'              => '11897256',
    'designID'        => '68',
    'productID'       => '347',
    'lastUpdated'     => '2013-09-09 21:43:11',
    'description'     => 'This is the Hookah Kitty, Love it!',
    'articleImageURL' => '//image.spreadshirt.com/image-server/v1/products/104563319/views/1',
    'numberSold'      => '0',
    'baseRetail'      => 11,
);
$productArray = array(
    'ID'             => '347',
    'cost'           => 9.22,
    'type'           => 'female',
    'sizesAvailable' => 'S,M,L,XL,XXL',
);

$orderItem = $orderItemFactory->create(1, $designArray, $articleArray, $productArray, 'M', 1, 17);

$array = $orderItemFactory->convertObjectToArray($orderItem);

Debug::dump($array);