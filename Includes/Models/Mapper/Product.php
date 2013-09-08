<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Mapper;

use Currency;

class Product extends MapperBase implements MapperInterface
{

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param int      $ID
     * @param Currency $cost
     * @param string   $type
     * @param string[] $sizesAvailable
     *
     * @return \Object\Product
     */
    public function create($ID = null, $cost = null, $type = null, $sizesAvailable = null)
    {
        $array = [
            'ID'             => $ID,
            'cost'           => $cost,
            'type'           => $type,
            'sizesAvailable' => $sizesAvailable
        ];

        return parent::convertArrayToObject('\Object\Product', $array);
    }

    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);

        /** @var Currency $cost */
        $cost                    = $array['cost'];
        $array['cost']           = $cost->getDecimal();
        $array['sizesAvailable'] = implode(',', $array['sizesAvailable']);

        return $array;
    }

    public function convertArrayToObject($array)
    {
        $array['cost']           = new Currency($array['cost']);
        $array['sizesAvailable'] = explode(',', $array['sizesAvailable']);

        return parent::convertArrayToObject($array);
    }
}