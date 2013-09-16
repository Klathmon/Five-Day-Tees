<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace Object;

use Currency;

class Order implements ObjectInterface
{
    /** @var int */
    protected $ID;
    /** @var Customer */
    protected $customer;
    /** @var Address */
    protected $address;
    /** @var string */
    protected $status;
    /** @var Currency */
    protected $orderTotal;
    /** @var Currency */
    protected $taxTotal;
    /** @var Currency */
    protected $shippingTotal;
    /** @var Currency */
    protected $itemsTotal;
    /** @var Coupon */
    protected $coupon;
    /** @var string */
    protected $paypalCorrelationID;
    /** @var OrderItem[] */
    protected $orderItems;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return \Object\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return \Object\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @return \Object\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return \Currency
     */
    public function getItemsTotal()
    {
        return $this->itemsTotal;
    }

    /**
     * @return \Currency
     */
    public function getOrderTotal()
    {
        return $this->orderTotal;
    }

    /**
     * @return string
     */
    public function getPaypalCorrelationID()
    {
        return $this->paypalCorrelationID;
    }

    /**
     * @return \Currency
     */
    public function getShippingTotal()
    {
        return $this->shippingTotal;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return \Currency
     */
    public function getTaxTotal()
    {
        return $this->taxTotal;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \Object\OrderItem[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }


}