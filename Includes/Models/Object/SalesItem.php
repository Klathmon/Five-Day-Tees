<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/12/13
 */

namespace Object;

use Currency;

class SalesItem extends Design implements ObjectInterface
{
    /** @var string */
    protected $key;
    /** @var Article */
    protected $article;
    /** @var Product */
    protected $product;
    /** @var string */
    protected $size;
    /** @var int */
    protected $quantity;
    /** @var Currency */
    protected $purchasePrice;
    /** @var int */
    private $totalSold;
    /** @var string */
    private $category;

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return \Object\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return \Object\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return \Currency
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Add one to the quantity
     */
    public function addOne()
    {
        $this->setQuantity($this->getQuantity() + 1);
    }

    /**
     * @return int
     */
    public function getTotalSold()
    {
        return $this->totalSold;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
}