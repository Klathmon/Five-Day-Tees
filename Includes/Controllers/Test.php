<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

use Entity\Item;

echo 'Test Page!';
$spreadshirtItems = new SpreadshirtItems($database, $config);
$spreadshirtItems->getNewItems();

//$itemMapper = new \Mapper\Item($database);

/*
$item = new \Entity\Item();

$item->setName('Test shirt HONKEY!');
$item->setGender('male');
$item->setArticleID('9816861');
$item->setDesignID('51954635');
$item->setDescription('This is another test shirt, this time included by the PROGRAM!!!');
$item->setSalesLimit(100);
$item->setDisplayDate(DateTime::createFromFormat('U', $itemMapper->getNextDate()->format('U')));
$item->setCost('9.46');
$item->setRetail('11');
$item->setProductImage('oiquswhoief');
$item->setDesignImage('oqubwfegerg');
$item->setSizesAvailable(explode(',', 'XS,S,M,L,XL'));
$item->setLastUpdated(DateTime::createFromFormat('U', time() - 5000));
*/

//$items = $itemMapper->listAll();

//Debug::dump($items);
