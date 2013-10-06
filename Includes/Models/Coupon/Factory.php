<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/6/13
 */

namespace Coupon;

use \Currency;
use PDO;
use Traits\Database;

class Factory extends \Abstracts\Factory
{
    use Database;

    /**
     * @param string $code
     * 
     * @return Entity
     */
    public function getByCodeFromDatabase($code)
    {
        $statement = $this->database->prepare('SELECT * FROM Coupon WHERE code=:code LIMIT 1');
        
        $statement->bindValue(':code', $code, PDO::PARAM_STR);
        
        return $this->executeAndParse($statement)[0];
    }

    protected function convertObjectToArray($entity)
    {
        $array = parent::convertObjectToArray($entity);
        
        /** @var $array Currency[] */
        $array['amount'] = $array['amount']->getDecimal();
        
        return $array;
    }

    protected function convertArrayToObject($array)
    {
        $array['amount'] = Currency::createFromDecimal($array['amount']);
        
        return parent::convertArrayToObject($array);
    }


}