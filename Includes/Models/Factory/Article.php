<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use \Currency;
use \DateTime;


class Article extends FactoryBase implements FactoryInterface
{

    /**
     * Returns one object from the given ID
     *
     * @param string $ID
     *
     * @throws \Exception
     *
     * @return \Object\Article
     */
    public function getByID($ID)
    {
        $statement = $this->database->prepare('SELECT * FROM Article WHERE articleID=:ID LIMIT 1');
        $statement->bindValue(':ID', $ID);

        return $this->executeAndParse($statement)[0];
    }
    
    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);

        /** @var DateTime[] $array */
        $array['lastUpdated'] = $array['lastUpdated']->format('Y-m-d H:i:s');
        /** @var Currency[] $array */
        $array['baseRetail']  = $array['baseRetail']->getDecimal();

        return $array;
    }

    public function convertArrayToObject($array)
    {
        $array['lastUpdated'] = DateTime::createFromFormat('Y-m-d H:i:s', $array['lastUpdated']);
        $array['baseRetail']  = new Currency($array['baseRetail']);

        return parent::convertArrayToObject($array);
    }

}