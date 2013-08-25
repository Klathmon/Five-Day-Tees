<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

namespace Entity;

use DateTime;

class Item implements Entity
{
    /** @var int */
    private $ID;
    /** @var string */
    private $name;
    /** @var string */
    private $gender;
    /** @var string */
    private $articleID;
    /** @var string */
    private $designID;
    /** @var string */
    private $description;
    /** @var int */
    private $salesLimit;
    /** @var DateTime */
    private $displayDate;
    /** @var string */
    private $cost;
    /** @var string */
    private $retail;
    /** @var string */
    private $productImage;
    /** @var string */
    private $designImage;
    /** @var string[] */
    private $sizesAvailable;
    /** @var DateTime */
    private $lastUpdated;


    public function __construct($ID = null)
    {
        $this->ID = $ID;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getArticleID()
    {
        return $this->articleID;
    }

    /**
     * @param string $articleID
     */
    public function setArticleID($articleID)
    {
        $this->articleID = $articleID;
    }

    /**
     * @return string
     */
    public function getDesignID()
    {
        return $this->designID;
    }

    /**
     * @param string $designID
     */
    public function setDesignID($designID)
    {
        $this->designID = $designID;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getSalesLimit()
    {
        return $this->salesLimit;
    }

    /**
     * @param int $salesLimit
     */
    public function setSalesLimit($salesLimit)
    {
        $this->salesLimit = $salesLimit;
    }

    /**
     * @return DateTime
     */
    public function getDisplayDate()
    {
        return $this->displayDate;
    }

    /**
     * @param DateTime $displayDate
     */
    public function setDisplayDate($displayDate)
    {
        $this->displayDate = $displayDate;
    }

    /**
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param string $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return string
     */
    public function getRetail()
    {
        return $this->retail;
    }

    /**
     * @param string $retail
     */
    public function setRetail($retail)
    {
        $this->retail = $retail;
    }

    /**
     * @return string
     */
    public function getProductImage()
    {
        return $this->productImage;
    }

    /**
     * @param string $productImage
     */
    public function setProductImage($productImage)
    {
        $this->productImage = $productImage;
    }

    /**
     * @return string
     */
    public function getDesignImage()
    {
        return $this->designImage;
    }

    /**
     * @param string $designImage
     */
    public function setDesignImage($designImage)
    {
        $this->designImage = $designImage;
    }

    /**
     * @return string[]
     */
    public function getSizesAvailable()
    {
        return $this->sizesAvailable;
    }

    /**
     * @param string[] $sizesAvailable
     */
    public function setSizesAvailable($sizesAvailable)
    {
        $this->sizesAvailable = $sizesAvailable;
    }

    public function hasSize($size)
    {
        //TODO: add hasSize to Item class
    }

    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated($dateTime)
    {
        $this->lastUpdated = $dateTime;
    }
}