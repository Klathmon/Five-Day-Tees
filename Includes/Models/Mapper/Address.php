<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/6/13
 */

namespace Mapper;

use Entity\Entity;
use PDO;

class Address implements Mapper
{
    /** @var PDO */
    private $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getByID($ID)
    {
        $statement = $this->database->prepare('SELECT * FROM Addresses WHERE ID=:ID');

        $statement->bindValue(':ID', $ID, PDO::PARAM_INT);
        $statement->execute();

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function listAll($start = 0, $stop = NULL)
    {
        $statement = $this->database->prepare('SELECT * FROM Addresses');

        $statement->execute();

        $array = [];

        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $address){
            $array[] = $this->createEntity($address);
        }

        return $array;
    }

    public function delete(Entity $entity)
    {
        $ID = $entity->getID();

        $sql
            = <<<SQL
DELETE FROM Addresses WHERE ID = :ID LIMIT 1
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':ID', $ID, PDO::PARAM_INT);

        $statement->execute();
    }

    public function persist(Entity $entity)
    {
        /** @var \Entity\Address $entity */

        $sql = <<<SQL
INSERT INTO Addresses 
  VALUES ( :ID, :LastName, :FirstName, :Company, :Phone, :Address1, :Address2, :City, :State, :Zip, :Country )
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':ID', $entity->getID(), PDO::PARAM_INT);
        $statement->bindValue(':LastName', $entity->getLastName(), PDO::PARAM_STR);
        $statement->bindValue(':FirstName', $entity->getFirstName(), PDO::PARAM_STR);
        $statement->bindValue(':Company', $entity->getCompany(), PDO::PARAM_STR);
        $statement->bindValue(':Phone', $entity->getPhone(), PDO::PARAM_STR);
        $statement->bindValue(':Address1', $entity->getAddress1(), PDO::PARAM_STR);
        $statement->bindValue(':Address2', $entity->getAddress2(), PDO::PARAM_STR);
        $statement->bindValue(':City', $entity->getCity(), PDO::PARAM_STR);
        $statement->bindValue(':State', $entity->getStateCode(), PDO::PARAM_STR);
        $statement->bindValue(':Zip', $entity->getZip(), PDO::PARAM_STR);
        $statement->bindValue(':Country', $entity->getCountry(), PDO::PARAM_STR);

        $statement->execute();

    }

    /**
     * This will add the address to the database if it doesn't exist.
     *
     * Either way it returns the Addresses' ID
     *
     * @param \Entity\Address $address
     *
     * @return int
     */
    public function addNewAddress(\Entity\Address $address)
    {
        try{
            //Try to save it to the database
            $this->persist($address);
            $ID = $this->database->lastInsertId();
        } catch(\PDOException $e){
            //If it fails, then it must be a duplicate, so select it from the database

            $sql = <<<SQL
SELECT id FROM Addresses
  WHERE LastName=:LastName
    AND FirstName=:FirstName
    AND Company=:Company
    AND Phone=:Phone
    AND Address1=:Address1
    AND Address2=:Address2
    AND City=:City
    AND State=:State
    AND Zip=:Zip
    AND Country=:Country
SQL;

            $statement = $this->database->prepare($sql);

            $statement->bindValue(':LastName', $address->getLastName(), PDO::PARAM_STR);
            $statement->bindValue(':FirstName', $address->getFirstName(), PDO::PARAM_STR);
            $statement->bindValue(':Company', $address->getCompany(), PDO::PARAM_STR);
            $statement->bindValue(':Phone', $address->getPhone(), PDO::PARAM_STR);
            $statement->bindValue(':Address1', $address->getAddress1(), PDO::PARAM_STR);
            $statement->bindValue(':Address2', $address->getAddress2(), PDO::PARAM_STR);
            $statement->bindValue(':City', $address->getCity(), PDO::PARAM_STR);
            $statement->bindValue(':State', $address->getStateCode(), PDO::PARAM_STR);
            $statement->bindValue(':Zip', $address->getZip(), PDO::PARAM_STR);
            $statement->bindValue(':Country', $address->getCountry(), PDO::PARAM_STR);

            $statement->execute();

            $ID = $statement->fetch(PDO::FETCH_NUM)[0];
        }
        
        $address->setID($ID);

        return $address;
    }

    /**
     * Create a new entity out of the data
     *
     * @param array $data The array of data to fill the object with
     *
     * @return \Entity\Address
     */
    private function createEntity($data)
    {
        if($data === FALSE){
            $entity = FALSE;
        } else{
            $entity = new \Entity\Address($data['ID']);

            $entity->setLastName($data['LastName']);
            $entity->setFirstName($data['FirstName']);
            $entity->setCompany($data['Company']);
            $entity->setPhone($data['Phone']);
            $entity->setAddress1($data['Address1']);
            $entity->setAddress2($data['Address2']);
            $entity->setCity($data['City']);
            $entity->setStateCode($data['State']);
            $entity->setZip($data['Zip']);
        }

        return $entity;
    }
}