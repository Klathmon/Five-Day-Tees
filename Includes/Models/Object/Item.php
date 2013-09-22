<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

class Item implements ObjectInterface
{
    /** @var string */
    protected $ID;
    /** @var Article */
    protected $article;
    /** @var Product */
    protected $product;
    /** @var Design */
    protected $design;
    /** @var int */
    protected $totalSold;

    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return \Object\Article
     */
    public function article()
    {
        return $this->article;
    }

    /**
     * @return \Object\Design
     */
    public function design()
    {
        return $this->design;
    }

    /**
     * @return \Object\Product
     */
    public function product()
    {
        return $this->product;
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
    public function getURLName()
    {
        return urlencode(str_replace(' ', '_', $this->design()->getName()));
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return "//" . $_SERVER['HTTP_HOST'] . '/Item/' . $this->getURLName();
    }
}