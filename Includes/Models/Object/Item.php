<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

class Item extends ObjectBase
{
    /** @var Design */
    protected $design;
    /** @var Article[] */
    protected $articles;
    /** @var Product[] */
    protected $products;

    /**
     * @return \Object\Design
     */
    public function getDesign()
    {
        return $this->design;
    }

    /**
     * @return \Object\Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @return \Object\Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $ID
     *
     * @return Article
     */
    public function getArticle($ID)
    {
        return $this->articles[$ID];
    }

    /**
     * @param $ID
     *
     * @return Product
     */
    public function getProduct($ID)
    {
        return $this->products[$ID];
    }
    
    
}