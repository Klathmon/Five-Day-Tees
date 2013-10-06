<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

use \Currency;

class Product implements ObjectInterface
{
    /** @var string */
    protected $productID;
    /** @var string */
    protected $size;
    /** @var Currency */
    protected $cost;
    /** @var string */
    protected $type;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->getProductID() . $this->getSize();
    }

    /**
     * @return string
     */
    public function getProductID()
    {
        return $this->productID;
    }

    /**
     * @return \string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return \Currency
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}