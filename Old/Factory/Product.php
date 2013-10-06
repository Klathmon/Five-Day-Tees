<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use Currency;
use PDO;

class Product extends FactoryBase implements FactoryInterface
{


    /**
     * Returns all the objects from the given ID
     *
     * @param string $ID
     *
     * @throws \Exception
     *
     * @return \Object\Product[]
     */
    public function getByID($ID)
    {
        $statement = $this->database->prepare('SELECT * FROM Product WHERE productID=:ID LIMIT 1');
        $statement->bindValue(':ID', $ID);
        
        return $this->executeAndParse($statement);
    }
    
    
    public function getByKey($productID, $size)
    {
        $sql = <<<SQL
SELECT * FROM Product WHERE productID=:productID AND size=:size LIMIT 1
SQL;
        
        $statement = $this->database->prepare($sql);
        
        $statement->bindValue(':productID', $productID, PDO::PARAM_STR);
        $statement->bindValue(':size', $size, PDO::PARAM_STR);

        return $this->executeAndParse($statement)[0];

    }
    
    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);
        
        /** @var Currency[] $array */
        $array['cost'] = $array['cost']->getDecimal();
        
        return $array;
    }

    public function convertArrayToObject($array)
    {
        $array['cost'] = Currency::createFromDecimal($array['cost']);
        
        return parent::convertArrayToObject($array);
    }

}