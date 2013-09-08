<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Greg
 * Date: 9/7/13
 * Time: 11:15 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Entity;

use \Entity\Address;
use \DateTime;


class Order implements Entity
{

    /** @var int */
    private $ID;
    /** @var Address */
    private $address;
    /** @var DateTime */
    private $createdDateTime;
    /** @var DateTime */
    private $lastUpdatedDateTime;
    /** @var string */
    private $status;
    /** @var string */
    private $email;
    /** @var string */
    private $itemsTotal;
    /** @var string */
    private $taxTotal;
    /** @var string */
    private $shippingTotal;
    /** @var string */
    private $orderTotal;

    public function __construct($ID = null)
    {
        $this->ID = $ID;
    }

    /**
     * @param int $ID
     */
    public function setID($ID)
    {
        $this->ID = $ID;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param DateTime $dateTime
     */
    public function setCreated(DateTime $dateTime)
    {
        $this->createdDateTime = $dateTime;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->createdDateTime;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $itemsTotal
     */
    public function setItemsTotal($itemsTotal)
    {
        $this->itemsTotal = $itemsTotal;
    }

    /**
     * @return string
     */
    public function getItemsTotal()
    {
        return $this->itemsTotal;
    }

    /**
     * @param DateTime $dateTime
     */
    public function setLastUpdated(DateTime $dateTime)
    {
        $this->lastUpdatedDateTime = $dateTime;
    }

    /**
     * @return DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdatedDateTime;
    }

    /**
     * @param string $orderTotal
     */
    public function setOrderTotal($orderTotal)
    {
        $this->orderTotal = $orderTotal;
    }

    /**
     * @return string
     */
    public function getOrderTotal()
    {
        return $this->orderTotal;
    }

    /**
     * @param string $shippingTotal
     */
    public function setShippingTotal($shippingTotal)
    {
        $this->shippingTotal = $shippingTotal;
    }

    /**
     * @return string
     */
    public function getShippingTotal()
    {
        return $this->shippingTotal;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $taxTotal
     */
    public function setTaxTotal($taxTotal)
    {
        $this->taxTotal = $taxTotal;
    }

    /**
     * @return string
     */
    public function getTaxTotal()
    {
        return $this->taxTotal;
    }
    
    
}