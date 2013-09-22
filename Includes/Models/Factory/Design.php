<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use DateTime;
use PDO;
use Traits\SQLStatements;

class Design extends FactoryBase implements FactoryInterface
{

    /**
     * Returns one object from the given ID
     *
     * @param int $ID
     *
     * @throws \Exception
     *
     * @return \Object\Design
     */
    public function getByID($ID)
    {
        $statement = $this->database->prepare('SELECT * FROM Design WHERE designID=:ID LIMIT 1');
        $statement->bindValue(':ID', $ID);

        return $this->executeAndParse($statement)[0];
    }
    
    /**
     * @param string $name
     *
     * @return \Object\Design
     * @throws \Exception
     */
    public function getByName($name)
    {
        $statement = $this->database->prepare('SELECT * FROM Design WHERE name=:name LIMIT 1');
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        
        return $this->executeAndParse($statement)[0];
    }

    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);
        
        /** @var DateTime[] $array */
        $array['displayDate'] = $array['displayDate']->format('Y-m-d');
        
        return $array;
    }

    public function convertArrayToObject($array)
    {
        $array['displayDate'] = DateTime::createFromFormat('Y-m-d', $array['displayDate']);
        
        return parent::convertArrayToObject($array);
    }


}