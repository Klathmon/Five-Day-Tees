<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/6/13
 */

namespace CartItem;

use PDO;
use PDOStatement;

class Factory extends \Item\Factory
{

    /**
     * @param string $ID
     * @param array  $passThru
     *
     * @return Entity
     */
    public function getByIDFromDatabase($ID, $passThru = null)
    {
        $passThru['size'] = $this->getPartsFromID($ID)['size'];

        return parent::getByIDFromDatabase($ID, $passThru);
    }

    protected function convertArrayToObject($array, $passThru = null)
    {
        $passThru['cartitemID'] = $this->getIDFromParts(
            [
                'articleID' => $array['articleID'],
                'productID' => $array['productID'],
                'size'      => $passThru['size']
            ]
        );
        
        if(!isset($passThru['quantity'])){
            $passThru['quantity']   = 1;
        }

        return parent::convertArrayToObject($array, $passThru);
    }
}