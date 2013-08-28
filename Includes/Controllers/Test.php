<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

use Entity\Item;

echo 'Test Page!';
$spreadshirtItems = new SpreadshirtItems($database, $config);
$spreadshirtItems->getNewItems();

echo "IT WORKED!";

$itemsMapper = new \Mapper\Item($database, $config);

$items = $itemsMapper->getQueue(true);

foreach ($items as $item) {
    $name   = $item->getName();
    $gender = $item->getGender();
    $date   = $item->getDisplayDate()->format('Y-m-d');

    Debug::dump($name, $gender, $date);
}