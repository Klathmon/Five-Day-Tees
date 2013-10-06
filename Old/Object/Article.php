<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

use \Currency;
use \DateTime;

class Article implements ObjectInterface
{
    /** @var string */
    protected $articleID;
    /** @var int */
    protected $designID;
    /** @var DateTime */
    protected $lastUpdated;
    /** @var string */
    protected $description;
    /** @var string */
    protected $articleImageURL;
    /** @var int */
    protected $numberSold;
    /** @var Currency */
    protected $baseRetail;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->articleID;
    }

    /**
     * @return string
     */
    public function getArticleID()
    {
        return $this->articleID;
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
     * @return Currency
     */
    public function getBaseRetail()
    {
        return $this->baseRetail;
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

    /**
     * @param Currency $baseRetail
     */
    public function setBaseRetail($baseRetail)
    {
        $this->baseRetail = $baseRetail;
    }

    /**
     * Returns the Article Image with the given parameters
     *
     * @param        $x
     * @param        $y
     * @param string $format
     *
     * @return string
     */
    public function getFormattedArticleImage($x, $y, $format = 'png')
    {
        return $this->getArticleImageURL() . ",width=$x,height=$y,mediaType=$format";
    }
}