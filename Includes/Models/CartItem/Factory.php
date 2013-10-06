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
     *
     * @return Entity
     */
    public function getByIDFromDatabase($ID)
    {
        $statement = $this->database->prepare(
            $this->sqlSelect . ' WHERE articleID=:articleID AND productID =:productID LIMIT 1'
        );

        $IDs = $this->getPartsFromID($ID);

        $statement->bindValue(':articleID', $IDs['articleID']);
        $statement->bindValue(':productID', $IDs['productID']);

        $statement->execute();

        return $this->executeAndParse($statement, ['size' => $this->getPartsFromID($ID)['size']])[0];
    }

    protected function convertArrayToObject($array, $passThru = null)
    {
        $passThru['cartitemID'] = $this->getIDFromParts(
            [
                'articleID' => $array['articleID'],
                'productID' => $array['productID'],
                'size' => $passThru['size']
            ]
        );
        $passThru['quantity'] = 1;

        return parent::convertArrayToObject($array, $passThru);
    }
}