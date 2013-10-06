<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Item;

use Currency;

abstract class Entity extends \Abstracts\Entity
{
    /** @var string */
    protected $itemID;
    /** @var \Article\Entity */
    protected $article;
    /** @var \Product\Entity */
    protected $product;
    /** @var int */
    protected $totalSold;
    /** @var string */
    protected $category;
    /** @var Currency */
    protected $currentPrice;

    /**
     * @return string
     */
    public function getItemID()
    {
        return $this->itemID;
    }

    /**
     * @return \Article\Entity
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return \Product\Entity
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Currency
     */
    public function getCurrentPrice()
    {
        return $this->currentPrice;
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