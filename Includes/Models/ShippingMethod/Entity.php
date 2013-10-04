<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/4/13
 */

namespace ShippingMethod;

use Currency;
use Exception;

class Entity extends \Abstracts\Entity
{
    /** @var int */
    protected $shippingMethodID;
    /** @var string */
    protected $name;
    /** @var int */
    protected $estimatedDaysLow;
    /** @var int */
    protected $estimatedDaysHigh;
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
    protected $disable;


    /**
     * @param Currency $subtotal
     *
     * @return Currency
     * @throws \Exception
     */
    public function calculateShippingPrice(Currency $subtotal)
    {
        $subtotalCents = $subtotal->getCents();

        if ($subtotalCents <= $this->getTier1PriceLimit()->getCents()) {
            $shippingCost = $this->getTier1Cost();
        } elseif ($subtotalCents <= $this->getTier2PriceLimit()->getCents()) {
            $shippingCost = $this->getTier2Cost();
        } elseif ($subtotalCents <= $this->getTier3PriceLimit()->getCents()) {
            $shippingCost = $this->getTier3Cost();
        } else {
            throw new Exception('Order Total is too high!', 2);
        }

        return $shippingCost;
    }

    /**
     * @param int $estimatedDaysHigh
     */
    public function setEstimatedDaysHigh($estimatedDaysHigh)
    {
        $this->estimatedDaysHigh = $estimatedDaysHigh;
    }

    /**
     * @return int
     */
    public function getEstimatedDaysHigh()
    {
        return $this->estimatedDaysHigh;
    }

    /**
     * @param int $estimatedDaysLow
     */
    public function setEstimatedDaysLow($estimatedDaysLow)
    {
        $this->estimatedDaysLow = $estimatedDaysLow;
    }

    /**
     * @return int
     */
    public function getEstimatedDaysLow()
    {
        return $this->estimatedDaysLow;
    }

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disable = $disabled;
    }

    /**
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disable;
    }
    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->getDisabled();
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