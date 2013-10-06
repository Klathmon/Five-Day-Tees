<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace OrderItem;

use Currency;
use Exception;

class Entity extends \CartItem\Entity
{
    /** @var int */
    protected $orderitemID;
    /** @var string */
    protected $orderID;
    /** @var Currency */
    protected $purchasePrice;

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @return Currency
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    final public function getCurrentPrice()
    {
        throw new Exception('Can\'t the the current price of something already sold!');
    }
}