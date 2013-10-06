<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Coupon;

use Currency;

class Entity extends \Abstracts\Entity
{
    /** @var int */
    protected $couponID;
    /** @var string */
    protected $code;
    /** @var Currency */
    protected $amount;
    /** @var int */
    protected $usesRemaining;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return \Currency
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getUsesRemaining()
    {
        return $this->usesRemaining;
    }

    /**
     * @param int $usesRemaining
     */
    public function setUsesRemaining($usesRemaining)
    {
        $this->usesRemaining = $usesRemaining;
    }

    /**
     * @param \Currency $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}