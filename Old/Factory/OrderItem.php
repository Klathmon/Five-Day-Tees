<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace Factory;

use Currency;

class OrderItem extends SalesItem implements FactoryInterface
{
    public function create($orderID = null, $designArray = null, $articleArray = null, $productArray = null, $size = null, $quantity = null, $purchasePrice = null)
    {
        $bigArray = $designArray;
        $bigArray['article'] = $articleArray;
        $bigArray['product'] = $productArray;
        $bigArray['key'] = $this->getKey($articleArray['ID'], $size);
        $bigArray['orderID'] = $orderID;
        $bigArray['size'] = $size;
        $bigArray['quantity'] = $quantity;
        $bigArray['purchasePrice'] = $purchasePrice;

        $orderItem = $this->convertArrayToObject($bigArray);

        return $orderItem;
    }

    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);

        $storageArray['orderID']       = $array['orderID'];
        $storageArray['articleID']     = $array['article']['ID'];
        $storageArray['size']          = $array['size'];
        $storageArray['quantity']      = $array['quantity'];
        $storageArray['purchasePrice'] = $array['purchasePrice'];

        return $storageArray;
    }
}