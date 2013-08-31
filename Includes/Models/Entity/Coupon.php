<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

namespace Entity;

class Coupon implements Entity
{
    /** @var string */
    private $ID;
    /** @var bool */
    private $isPercent;
    /** @var double */
    private $amount;
    /** @var int */
    private $usesRemaining;


    public function __construct($code)
    {
        $this->ID = $code;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function getCode()
    {
        return $this->getID();
    }

    public function isPercent()
    {
        if ($this->isPercent === true) {
            return true;
        } else {
            return false;
        }
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getUsesRemaining()
    {
        return $this->usesRemaining;
    }

    public function useCoupon()
    {
        $this->usesRemaining -= 1;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function makePercent()
    {
        $this->isPercent = true;
    }

    public function makeFlatAmount()
    {
        $this->isPercent = false;
    }

    public function setUsesRemaining($usesRemaining)
    {
        $this->usesRemaining = $usesRemaining;
    }
}