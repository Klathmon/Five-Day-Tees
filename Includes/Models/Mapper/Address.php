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

    public function listAll($start = 0, $stop = null)
    {
        // TODO: Implement listAll() method.
    }

    public function delete(Entity $entity)
    {
        // TODO: Implement delete() method.
    }

    public function persist(Entity $entity)
    {
        // TODO: Implement persist() method.
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
        if ($data === false) {
            $entity = false;
        } else {
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