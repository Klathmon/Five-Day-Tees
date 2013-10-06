<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace Factory;

use Exception;
use PDO;

class Customer extends FactoryBase implements FactoryInterface
{

    /**
     * @param string $paypalPayerID
     *
     * @return \Object\Customer
     * @throws \Exception
     */
    public function getByPayPalPayerID($paypalPayerID)
    {
        $statement = $this->database->prepare('SELECT * FROM Customer WHERE paypalPayerID = :paypalPayerID LIMIT 1');

        $statement->bindValue(':paypalPayerID', $paypalPayerID, PDO::PARAM_STR);

        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new Exception('Customer does not exist!');
        } else {
            $customer = $this->convertArrayToObject($row);
        }

        return $customer;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $companyName
     * @param string $phoneNumber
     * @param string $email
     *
     * @return \Object\Customer
     * @throws \Exception
     */
    public function getByCustomerInfo($firstName, $lastName, $companyName, $phoneNumber, $email)
    {
        $sql
            = <<<SQL
SELECT * 
  FROM Customer
  WHERE firstName = :firstName
    AND lastName = :lastName
    AND companyName = :companyName
    AND phoneNumber = :phoneNumber
    AND email = :email
  LIMIT 1
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':firstName', $firstName, PDO::PARAM_STR);
        $statement->bindValue(':lastName', $lastName, PDO::PARAM_STR);
        $statement->bindValue(':companyName', $companyName, PDO::PARAM_STR);
        $statement->bindValue(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new Exception('Customer does not exist!');
        } else {
            $customer = $this->convertArrayToObject($row);
        }

        return $customer;

    }

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param int    $ID
     * @param string $paypalPayerID
     * @param string $firstName
     * @param string $lastName
     * @param string $companyName
     * @param string $phoneNumber
     * @param string $email
     * @param bool   $allowMarketing
     *
     * @return \Object\Customer
     */
    public function create(
        $ID = null,
        $paypalPayerID = null,
        $firstName = null,
        $lastName = null,
        $companyName = null,
        $phoneNumber = null,
        $email = null,
        $allowMarketing = null
    ){
        $array = [
            'ID'             => $ID,
            'paypalPayerID'      => $paypalPayerID,
            'firstName'      => $firstName,
            'lastName'       => $lastName,
            'companyName'    => $companyName,
            'phoneNumber'    => $phoneNumber,
            'email'          => $email,
            'allowMarketing' => $allowMarketing
        ];

        return parent::convertArrayToObject($array);
    }
}