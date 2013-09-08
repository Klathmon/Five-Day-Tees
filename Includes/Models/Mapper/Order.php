<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Mapper;

use Entity\Entity;
use \PDO;
use \DateTime;

class Order implements Mapper
{
    /** @var PDO */
    private $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
        $this->addressMapper = new \Mapper\Address($this->database);
    }

    public function getByID($ID)
    {
        $sql = <<<SQL
SELECT *
  FROM Orders 
  WHERE ID=:ID
  LIMIT 1
SQL;
        
        $statement = $this->database->prepare($sql);

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
     * @param array $data
     * 
     * @return \Entity\Object
     */
    private function createEntity($data){

        if($data === FALSE){
            $entity = FALSE;
        } else{
            $entity = new \Entity\Order($data['ID']);
            
            $entity->setAddress($this->addressMapper->getByID($data['AddressID']));
            $entity->setCreated(DateTime::createFromFormat('Y-m-d H:i:s', $data['Created']));
            $entity->setLastUpdated(DateTime::createFromFormat('Y-m-d H:i:s', $data['LastUpdated']));
            $entity->setEmail($data['Email']);
            $entity->setItemsTotal($data['ItemsTotal']);
            $entity->setOrderTotal($data['OrderTotal']);
            $entity->setShippingTotal($data['ShippingTotal']);
            $entity->setStatus($data['Status']);
            $entity->setTaxTotal($data['TaxTotal']);
        }
        
        return $entity;
    }
}