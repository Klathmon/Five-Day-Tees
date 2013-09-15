<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/15/13
 */

namespace Factory;

use Exception;
use PDO;

class Address extends FactoryBase implements FactoryInterface
{

    /**
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     *
     * @return \Object\Address
     */
    public function fetchOrCreate($address1, $address2, $city, $state, $zip, $country)
    {
        try{
            $address = $this->getByAddressInfo($address1, $address2, $city, $state, $zip, $country);
        } catch(Exception $e){
            $address = $this->create(null, $address1, $address2, $city, $state, $zip, $country);
            $this->persist($address);
        }

        return $address;
    }

    /**
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     *
     * @return \Object\Address
     * @throws \Exception
     */
    public function getByAddressInfo($address1, $address2, $city, $state, $zip, $country)
    {
        $sql
                   = <<<SQL
SELECT * 
  FROM Address
  WHERE address1 = :address1
    AND address2 = :address2
    AND city = :city
    AND state = :state
    AND zip = :zip
    AND country = :country
  LIMIT 1
SQL;
        $statement = $this->database->prepare($sql);

        $statement->bindValue(':address1', $address1, PDO::PARAM_STR);
        $statement->bindValue(':address2', $address2, PDO::PARAM_STR);
        $statement->bindValue(':city', $city, PDO::PARAM_STR);
        $statement->bindValue(':state', $state, PDO::PARAM_STR);
        $statement->bindValue(':zip', $zip, PDO::PARAM_STR);
        $statement->bindValue(':country', $country, PDO::PARAM_STR);

        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new Exception('Address does not exist!');
        } else {
            $address = $this->convertArrayToObject($row);
        }

        return $address;
    }

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param int    $ID
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     *
     * @return \Object\Address;
     */
    public function create(
        $ID = null,
        $address1 = null,
        $address2 = null,
        $city = null,
        $state = null,
        $zip = null,
        $country = null
    ){
        $array = [
            'ID'       => $ID,
            'address1' => $address1,
            'address2' => $address2,
            'city'     => $city,
            'state'    => $state,
            'zip'      => $zip,
            'country'  => $country
        ];

        return parent::convertArrayToObject($array);
    }
}