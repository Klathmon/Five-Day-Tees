<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/4/13
 */

namespace Entity;

use Exception;

class ShippingMethod implements Entity
{
    /** @var int */
    private $ID;
    /** @var string */
    private $name;
    /** @var int */
    private $daysLow;
    /** @var int */
    private $daysHigh;
    /** @var double */
    private $tier1Cost;
    /** @var double */
    private $tier2Cost;
    /** @var double */
    private $tier3Cost;
    /** @var double */
    private $tier1PriceLimit;
    /** @var double */
    private $tier2PriceLimit;
    /** @var double */
    private $tier3PriceLimit;
    /** @var bool */
    private $disabled;


    public function __construct($ID)
    {
        $this->ID = $ID;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function calculateShippingPrice($subtotal)
    {
        $shippingCost = 0;

        if ($subtotal <= $this->getTier1PriceLimit()) {
            $shippingCost = $this->getTier1Cost();
        } elseif ($subtotal <= $this->getTier2PriceLimit()) {
            $shippingCost = $this->getTier2Cost();
        } elseif ($subtotal <= $this->getTier3PriceLimit()) {
            $shippingCost = $this->getTier3Cost();
        } else {
            throw new Exception('Order Total is too high!', 2);
        }

        return $shippingCost;
    }

    /**
     * @param int $daysHigh
     */
    public function setDaysHigh($daysHigh)
    {
        $this->daysHigh = $daysHigh;
    }

    /**
     * @return int
     */
    public function getDaysHigh()
    {
        return $this->daysHigh;
    }

    /**
     * @param int $daysLow
     */
    public function setDaysLow($daysLow)
    {
        $this->daysLow = $daysLow;
    }

    /**
     * @return int
     */
    public function getDaysLow()
    {
        return $this->daysLow;
    }

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = ($disabled ? true : false);
    }

    /**
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param float $tier1Cost
     */
    public function setTier1Cost($tier1Cost)
    {
        $this->tier1Cost = $tier1Cost;
    }

    /**
     * @return float
     */
    public function getTier1Cost()
    {
        return $this->tier1Cost;
    }

    /**
     * @param float $tier1PriceLimit
     */
    public function setTier1PriceLimit($tier1PriceLimit)
    {
        $this->tier1PriceLimit = $tier1PriceLimit;
    }

    /**
     * @return float
     */
    public function getTier1PriceLimit()
    {
        return $this->tier1PriceLimit;
    }

    /**
     * @param float $tier2Cost
     */
    public function setTier2Cost($tier2Cost)
    {
        $this->tier2Cost = $tier2Cost;
    }

    /**
     * @return float
     */
    public function getTier2Cost()
    {
        return $this->tier2Cost;
    }

    /**
     * @param float $tier2PriceLimit
     */
    public function setTier2PriceLimit($tier2PriceLimit)
    {
        $this->tier2PriceLimit = $tier2PriceLimit;
    }

    /**
     * @return float
     */
    public function getTier2PriceLimit()
    {
        return $this->tier2PriceLimit;
    }

    /**
     * @param float $tier3Cost
     */
    public function setTier3Cost($tier3Cost)
    {
        $this->tier3Cost = $tier3Cost;
    }

    /**
     * @return float
     */
    public function getTier3Cost()
    {
        return $this->tier3Cost;
    }

    /**
     * @param float $tier3PriceLimit
     */
    public function setTier3PriceLimit($tier3PriceLimit)
    {
        $this->tier3PriceLimit = $tier3PriceLimit;
    }

    /**
     * @return float
     */
    public function getTier3PriceLimit()
    {
        return $this->tier3PriceLimit;
    }

}