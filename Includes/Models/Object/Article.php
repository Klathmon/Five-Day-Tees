<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

use \DateTime;

class Article implements ObjectInterface
{
    /** @var int */
    protected $ID;
    /** @var int */
    protected $designID;
    /** @var int */
    protected $productID;
    /** @var DateTime */
    protected $lastUpdated;
    /** @var string */
    protected $description;
    /** @var string */
    protected $articleImageURL;
    /** @var int */
    protected $numberSold;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return int
     */
    public function getDesignID()
    {
        return $this->designID;
    }

    /**
     * @return int
     */
    public function getProductID()
    {
        return $this->productID;
    }

    /**
     * @return string
     */
    public function getArticleImageURL()
    {
        return $this->articleImageURL;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * @return int
     */
    public function getNumberSold()
    {
        return $this->numberSold;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param \DateTime $lastUpdated
     */
    public function setLastUpdated(DateTime $lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @param int $numberSold
     */
    public function setNumberSold($numberSold)
    {
        $this->numberSold = $numberSold;
    }
}