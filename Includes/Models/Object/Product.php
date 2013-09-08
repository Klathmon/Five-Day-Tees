<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

use \Currency;

class Product implements ObjectInterface
{
    /** @var int */
    protected $ID;
    /** @var Currency */
    protected $cost;
    /** @var string */
    protected $type;
    /** @var string[] */
    protected $sizesAvailable;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return \Currency
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return \string[]
     */
    public function getSizesAvailable()
    {
        return $this->sizesAvailable;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    
}