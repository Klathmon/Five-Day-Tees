<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/11/13
 */

namespace Object;

use Currency;

class ShippingMethod implements ObjectInterface
{
    /** @var int */
    protected $ID;
    /** @var string */
    protected $name;
    /** @var int */
    protected $daysLow;
    /** @var int */
    protected $daysHigh;
    /** @var Currency */
    protected $tier1Cost;
    /** @var Currency */
    protected $tier2Cost;
    /** @var Currency */
    protected $tier3Cost;
    /** @var Currency */
    protected $tier1PriceLimit;
    /** @var Currency */
    protected $tier2PriceLimit;
    /** @var Currency */
    protected $tier3PriceLimit;
    /** @var bool */
    protected $disabled;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
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
        $this->disabled = $disabled;
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
     * @param Currency $tier1Cost
     */
    public function setTier1Cost($tier1Cost)
    {
        $this->tier1Cost = $tier1Cost;
    }

    /**
     * @return Currency
     */
    public function getTier1Cost()
    {
        return $this->tier1Cost;
    }

    /**
     * @param Currency $tier1PriceLimit
     */
    public function setTier1PriceLimit($tier1PriceLimit)
    {
        $this->tier1PriceLimit = $tier1PriceLimit;
    }

    /**
     * @return Currency
     */
    public function getTier1PriceLimit()
    {
        return $this->tier1PriceLimit;
    }

    /**
     * @param Currency $tier2Cost
     */
    public function setTier2Cost($tier2Cost)
    {
        $this->tier2Cost = $tier2Cost;
    }

    /**
     * @return Currency
     */
    public function getTier2Cost()
    {
        return $this->tier2Cost;
    }

    /**
     * @param Currency $tier2PriceLimit
     */
    public function setTier2PriceLimit($tier2PriceLimit)
    {
        $this->tier2PriceLimit = $tier2PriceLimit;
    }

    /**
     * @return Currency
     */
    public function getTier2PriceLimit()
    {
        return $this->tier2PriceLimit;
    }

    /**
     * @param Currency $tier3Cost
     */
    public function setTier3Cost($tier3Cost)
    {
        $this->tier3Cost = $tier3Cost;
    }

    /**
     * @return Currency
     */
    public function getTier3Cost()
    {
        return $this->tier3Cost;
    }

    /**
     * @param Currency $tier3PriceLimit
     */
    public function setTier3PriceLimit($tier3PriceLimit)
    {
        $this->tier3PriceLimit = $tier3PriceLimit;
    }

    /**
     * @return Currency
     */
    public function getTier3PriceLimit()
    {
        return $this->tier3PriceLimit;
    }
}