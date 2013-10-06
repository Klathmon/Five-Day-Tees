<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace Object;

class OrderItem extends SalesItem implements ObjectInterface
{
    /** @var int */
    protected $orderID;

    /**
     * @param int $orderID
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;
    }

    /**
     * @return int
     */
    public function getOrderID()
    {
        return $this->orderID;
    }
    
    
}