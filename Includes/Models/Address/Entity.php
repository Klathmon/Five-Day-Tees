<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace Address;

class Entity extends \Abstracts\Entity
{
    /** @var int */
    protected $addressID;
    /** @var string */
    protected $address1;
    /** @var string */
    protected $address2;
    /** @var string */
    protected $city;
    /** @var string */
    protected $state;
    /** @var string */
    protected $zip;
    /** @var string */
    protected $country;

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }
}