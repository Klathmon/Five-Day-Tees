<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

use \DateTime;


class Design implements ObjectInterface
{
    /** @var int */
    protected $ID;
    /** @var string */
    protected $name;
    /** @var DateTime */
    protected $displayDate;
    /** @var string */
    protected $designImageURL;
    /** @var int */
    protected $salesLimit;
    /** @var int */
    protected $votes;

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
    public function getDesignImageURL()
    {
        return $this->designImageURL;
    }

    /**
     * @return \DateTime
     */
    public function getDisplayDate()
    {
        return $this->displayDate;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSalesLimit()
    {
        return $this->salesLimit;
    }

    /**
     * @return int
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param \DateTime $displayDate
     */
    public function setDisplayDate(DateTime $displayDate)
    {
        $this->displayDate = $displayDate;
    }

    /**
     * @param int $salesLimit
     */
    public function setSalesLimit($salesLimit)
    {
        $this->salesLimit = $salesLimit;
    }

    /**
     * @param int $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
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
        return $this->getDesignImageURL() . ",width=$x,height=$y,mediaType=$format";
    }
}