<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Product;

use Currency;

class Entity extends \Abstracts\Entity
{
    /** @var string */
    protected $productID;
    /** @var int */
    protected $articleID;
    /** @var string */
    protected $description;
    /** @var string */
    protected $productImageURL;
    /** @var Currency */
    protected $cost;
    /** @var Currency */
    protected $retail;
    /** @var int */
    protected $sold;
    /** @var string */
    protected $type;
    /** @var string[] */
    protected $sizes;

    /**
     * @return int
     */
    public function getArticleID()
    {
        return $this->articleID;
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
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * @return string
     */
    public function getProductImageURL()
    {
        return $this->productImageURL;
    }

    /**
     * @return \Currency
     */
    public function getRetail()
    {
        return $this->retail;
    }

    /**
     * @return \string[]
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @return int
     */
    public function getSold()
    {
        return $this->sold;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param \Currency $retail
     */
    public function setRetail($retail)
    {
        $this->retail = $retail;
    }

    /**
     * @param \string[] $sizes
     */
    public function setSizes($sizes)
    {
        $this->sizes = $sizes;
    }

    /**
     * @param int $sold
     */
    public function setSold($sold)
    {
        $this->sold = $sold;
    }

    public function getFormattedImage($x, $y, $format = 'png')
    {
        return $this->getProductImageURl() . ",width=$x,height=$y,mediaType=$format";
    }
}