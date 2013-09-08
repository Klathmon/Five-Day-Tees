<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */


class Currency
{
    /** @var int */
    private $cents;
    
    public function __construct($decimal)
    {
        $this->cents = $decimal * 100;
    }
    
    public function getCents()
    {
        return $this->cents;
    }
    
    public function getDecimal()
    {
        return ($this->cents / 100);
    }
}