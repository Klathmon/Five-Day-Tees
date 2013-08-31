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
    protected $ID;
    /** @var string */
    protected $name;
    /** @var string */
    protected $gender;
    /** @var string */
    protected $articleID;
    /** @var string */
    protected $designID;
    /** @var string */
    protected $description;
    /** @var int */
    protected $salesLimit;
    /** @var DateTime */
    protected $displayDate;
    /** @var string */
    protected $cost;
    /** @var string */
    protected $retail;
    /** @var string */
    protected $productImage;
    /** @var string */
    protected $designImage;
    /** @var string[] */
    protected $sizesAvailable;
    /** @var DateTime */
    protected $lastUpdated;
    /** @var int */
    protected $numberSold;
    /** @var int */
    protected $votes;
    /** @var int */
    protected $totalSold;
    /** @var string */
    protected $category;


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

    /**
     * Gets the LastUpdated time
     *
     * @return DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Set the LastUpdated time
     *
     * @param DateTime $dateTime
     */
    public function setLastUpdated($dateTime)
    {
        $this->lastUpdated = $dateTime;
    }

    /**
     * Set the number sold
     *
     * @param int $numberSold
     */
    public function setNumberSold($numberSold)
    {
        $this->numberSold = $numberSold;
    }

    /**
     * Get the number sold
     *
     * @return int
     */
    public function getNumberSold()
    {
        $sold = $this->numberSold;

        if (is_null($sold)) {
            return 0;
        } else {
            return $sold;
        }
    }

    /**
     * Get the current Votes
     *
     * @param int $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * Set the current votes
     *
     * @return int
     */
    public function getVotes()
    {
        $votes = $this->votes;

        if (is_null($votes)) {
            return 0;
        } else {
            return $votes;
        }
    }

    /**
     * Returns the Design Image with the given parameters
     *
     * @param        $x
     * @param        $y
     * @param string $format
     *
     * @return string
     */
    public function getFormattedDesignImage($x, $y, $format = 'png')
    {
        return $this->getDesignImage() . ",width=$x,height=$y,mediaType=$format";
    }

    /**
     * Returns the Product Image with the given parameters
     *
     * @param        $x
     * @param        $y
     * @param string $format
     *
     * @return string
     */
    public function getFormattedProductImage($x, $y, $format = 'png')
    {
        return $this->getProductImage() . ",width=$x,height=$y,mediaType=$format";
    }

    public function setTotalSold($totalSold)
    {
        $this->totalSold = $totalSold;
    }

    public function getTotalSold()
    {
        return $this->totalSold;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getURL()
    {
        return urlencode(str_replace(' ', '_', $this->getName()));
    }

    public function getPermalink()
    {
        return "//" . $_SERVER['HTTP_HOST'] . '/Item/' . $this->getURL();
    }
}