<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/11/13
 */

namespace Object;

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
    /** @var double */
    protected $tier1Cost;
    /** @var double */
    protected $tier2Cost;
    /** @var double */
    protected $tier3Cost;
    /** @var double */
    protected $tier1PriceLimit;
    /** @var double */
    protected $tier2PriceLimit;
    /** @var double */
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