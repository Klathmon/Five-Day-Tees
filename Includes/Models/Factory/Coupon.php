<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/11/13
 */

namespace Factory;

use \Currency;
use PDO;

class Coupon extends FactoryBase implements FactoryInterface
{

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param string   $code
     * @param Currency $amount
     * @param int      $usesRemaining
     *
     * @return \Object\Coupon
     */
    public function create($code = null, $amount = null, $usesRemaining = null)
    {
        $array = [
            'ID'            => null,
            'code'          => $code,
            'amount'        => $amount,
            'usesRemaining' => $usesRemaining
        ];
        
        return parent::convertArrayToObject($array);
    }

    /**
     * @param string $code
     *
     * @return \Object\Coupon
     */
    public function getByCode($code)
    {
        $statement = $this->database->prepare('SELECT * FROM ' . $this->className . ' WHERE code=:code');

        $statement->bindValue(':code', $code, PDO::PARAM_STR);
        $statement->execute();

        $object = $this->convertArrayToObject($statement->fetch(PDO::FETCH_ASSOC));

        return $object;
    }

    /**
     * @param \Object\Coupon $object
     *
     * @return array
     */
    protected function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);
        
        /** @var Currency $amount */
        $amount = $array['amount'];
        
        $array['amount'] = $amount->getDecimal();
        
        return $array;
    }

    /**
     * @param array $array
     *
     * @return \Object\Coupon
     */
    protected function convertArrayToObject($array)
    {
        $array['amount'] = new Currency($array['amount']);
        
        return parent::convertArrayToObject($array);
    }
}