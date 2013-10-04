<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Article;

use DateTime;

class Entity extends \Abstracts\Entity
{
    /** @var int */
    protected $articleID;
    /** @var string */
    protected $name;
    /** @var DateTime */
    protected $date;
    /** @var string */
    protected $articleImageURL;
    /** @var int */
    protected $salesLimit;
    /** @var int */
    protected $votes;

    /**
     * @return string
     */
    public function getArticleImageURL()
    {
        return $this->articleImageURL;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    
    public function getFormattedImage($x, $y, $format = 'png')
    {
        return $this->getArticleImageURl() . ",width=$x,height=$y,mediaType=$format";
    }
}