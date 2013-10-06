<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace Customer;

class Entity extends \Abstracts\Entity
{
    /** @var int */
    protected $customerID;
    /** @var string */
    protected $paypalPayerID;
    /** @var string */
    protected $firstName;
    /** @var string */
    protected $lastName;
    /** @var string */
    protected $companyName;
    /** @var string */
    protected $phoneNumber;
    /** @var string */
    protected $email;
    /** @var bool */
    protected $allowMarketing;

    /**
     * @return string
     */
    public function getPaypalPayerID()
    {
        return $this->paypalPayerID;
    }

    /**
     * @return boolean
     */
    public function getAllowMarketing()
    {
        return $this->allowMarketing;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param boolean $allowMarketing
     */
    public function setAllowMarketing($allowMarketing)
    {
        $this->allowMarketing = $allowMarketing;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }
}