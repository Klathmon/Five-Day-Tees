<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Item;

class Entity extends \Abstracts\Entity
{
    /** @var string */
    protected $itemID;
    /** @var \Article\Entity */
    protected $article;
    /** @var \Product\Entity */
    protected $product;

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
}