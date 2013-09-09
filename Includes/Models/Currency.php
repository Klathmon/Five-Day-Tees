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
        return (string)($this->cents / 100);
    }
    
    public function getNiceFormat()
    {
        return number_format($this->getDecimal(), 2);
    }
    
    public function add($cents)
    {
        $this->cents += $cents;
    }
    
    public function subtract($cents)
    {
        $this->cents -= $cents;
    }
}