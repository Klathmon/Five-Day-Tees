<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/11/13
 */

namespace Object;

use Currency;

class Coupon implements ObjectInterface
{
    /** @var int */
    private $ID;
    /** @var string */
    private $code;
    /** @var Currency */
    private $amount;
    /** @var int */
    private $usesRemaining;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @param \Currency $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return \Currency
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $usesRemaining
     */
    public function setUsesRemaining($usesRemaining)
    {
        $this->usesRemaining = $usesRemaining;
    }

    /**
     * @return int
     */
    public function getUsesRemaining()
    {
        return $this->usesRemaining;
    }

    /**
     * Use a coupon, reduces the count by 1
     */
    public function useOne()
    {
        $this->setUsesRemaining($this->getUsesRemaining() - 1);
    }
}