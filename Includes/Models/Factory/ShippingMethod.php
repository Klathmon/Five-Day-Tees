<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/11/13
 */

namespace Factory;

use Currency;
use \PDO;

class ShippingMethod extends FactoryBase implements FactoryInterface
{

    /**
     * @param string $name
     *
     * @return \Object\ShippingMethod
     */
    public function getByName($name)
    {
        $statement = $this->database->prepare('SELECT * FROM ' . $this->className . ' WHERE name=:Name');

        $statement->bindValue(':Name', $name, PDO::PARAM_INT);
        $statement->execute();
        
        $object = $this->convertArrayToObject($statement->fetch(PDO::FETCH_ASSOC));
        
        return $object;
    }

    /**
     * Pass true into this function to display all shipping methods
     * Pass false or nothing to show only those that are enabled
     * 
     * @param bool $showDisabled
     *
     * @return \Object\ShippingMethod[]
     */
    public function getAll($showDisabled = false)
    {
        $rows = $this->database->query('SELECT * FROM ' . $this->className)->fetchAll(PDO::FETCH_ASSOC);


        foreach ($rows as $row){
            $array[] = $this->convertArrayToOBject($row);
        }

        return $array;
    }

    /**
     * @param \Object\ShippingMethod $object
     *
     * @return array
     */
    protected function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);

        /** @var Currency $tier1Cost */
        $tier1Cost = $array['tier1Cost'];
        /** @var Currency $tier2Cost */
        $tier2Cost = $array['tier2Cost'];
        /** @var Currency $tier3Cost */
        $tier3Cost = $array['tier3Cost'];
        /** @var Currency $tier1PriceLimit */
        $tier1PriceLimit = $array['tier1PriceLimit'];
        /** @var Currency $tier2PriceLimit */
        $tier2PriceLimit = $array['tier2PriceLimit'];
        /** @var Currency $tier3PriceLimit */
        $tier3PriceLimit = $array['tier3PriceLimit'];

        $array['tier1Cost']       = $tier1Cost->getDecimal();
        $array['tier2Cost']       = $tier2Cost->getDecimal();
        $array['tier3Cost']       = $tier3Cost->getDecimal();
        $array['tier1PriceLimit'] = $tier1PriceLimit->getDecimal();
        $array['tier2PriceLimit'] = $tier2PriceLimit->getDecimal();
        $array['tier3PriceLimit'] = $tier3PriceLimit->getDecimal();
        $array['disable'] = ($array['disable'] ? 1 : 0);
        
        return $array;
    }

    /**
     * @param array $array
     *
     * @return \Object\ShippingMethod
     */
    protected function convertArrayToObject($array)
    {
        $array['tier1Cost']       = new Currency($array['tier1Cost']);
        $array['tier2Cost']       = new Currency($array['tier2Cost']);
        $array['tier3Cost']       = new Currency($array['tier3Cost']);
        $array['tier1PriceLimit'] = new Currency($array['tier1PriceLimit']);
        $array['tier2PriceLimit'] = new Currency($array['tier2PriceLimit']);
        $array['tier3PriceLimit'] = new Currency($array['tier3PriceLimit']);
        $array['disable'] = ($array['disable'] ? true : false);
        
        return parent::convertArrayToObject($array);
    }


    /**
     * Stub, don't use
     */
    public function create(){ }
}