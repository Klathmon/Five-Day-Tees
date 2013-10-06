<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/4/13
 */

namespace ShippingMethod;

use Currency;
use Traits\Database;

/**
 * Class Factory
 *
 * @method Entity[] getAllFromDatabase();
 */
class Factory extends \Abstracts\Factory
{
    use Database;
    
    public function getAllEnabledFromDatabase()
    {
        /** @var Entity[] $shippingMethods */
        $shippingMethods = $this->getAllFromDatabase();

        foreach($shippingMethods as $shippingMethod){
            if(!$shippingMethod->getDisabled()){
                $return[] = $shippingMethod;
            }
        }
        
        return $return;
    }
    
    protected function convertObjectToArray($entity)
    {
        $array = parent::convertObjectToArray($entity);

        /** @var Currency[] $array */
        foreach ([1, 2, 3] as $number) {
            $array['tier' . $number . 'Cost']       = $array['tier' . $number . 'Cost']->getDecimal();
            $array['tier' . $number . 'PriceLimit'] = $array['tier' . $number . 'PriceLimit']->getDecimal();
        }
        
        $array['disable'] = ($array['disable'] ? 1 : 0);
        
        return $array;
    }

    protected function convertArrayToObject($array)
    {
        foreach ([1, 2, 3] as $number) {
            $array['tier' . $number . 'Cost']       = Currency::createFromDecimal($array['tier' . $number . 'Cost']);
            $array['tier' . $number . 'PriceLimit'] = Currency::createFromDecimal($array['tier' . $number . 'PriceLimit']);
        }
        
        $array['disable'] = ($array['disable'] ? true : false);

        return parent::convertArrayToObject($array);
    }


}