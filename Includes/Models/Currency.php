<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */


class Currency
{
    const MULTIPLIER = 100;
    
    /** @var int */
    private $cents;
    
    public static function createFromCents($cents)
    {
        return new self(self::convertCentsToDecimal($cents));
    }
    
    public static function createFromDecimal($decimal)
    {
        return new self($decimal);
    }
    
    public function getCents()
    {
        return $this->cents;
    }
    
    public function getDecimal()
    {
        return self::convertCentsToDecimal($this->cents);
    }


    private function __construct($decimal)
    {
        $this->cents = self::convertDecimalToCents($decimal);
    }
    
    private static function convertCentsToDecimal($cents)
    {
        return $cents / self::MULTIPLIER;
    }
    
    private static function convertDecimalToCents($decimal)
    {
        return $decimal * self::MULTIPLIER;
    }
}