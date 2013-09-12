<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use DateTime;
use PDO;

class Design extends FactoryBase implements FactoryInterface
{

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param int $ID
     * @param string $name
     * @param DateTime $displayDate
     * @param string $designImageURL
     * @param int $salesLimit
     * @param int $votes
     *
     * @return \Object\Design
     */
    public function create($ID = null, $name = null, $displayDate = null, $designImageURL = null, $salesLimit = null, $votes = null)
    {
        $array = [
            'ID' => $ID,
            'name' => $name,
            'displayDate' => $displayDate,
            'designImageURL' => $designImageURL,
            'salesLimit' => $salesLimit,
            'votes' => $votes,
        ];

        return parent::convertArrayToObject($array);
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
        $statement->execute();

        $array = $statement->fetch(PDO::FETCH_ASSOC);

        if ($array === false) {
            throw new \Exception('No object with that ID exists in the database');
        } else {
            $design = $this->convertArrayToObject($array);
        }
        
        return $design;
    }

    protected function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);
        
        /** @var DateTime $displayDate */
        $displayDate = $array['displayDate'];
        $array['displayDate'] = $displayDate->format('Y-m-d');
        
        return $array;
    }

    protected function convertArrayToObject($array)
    {
        $array['displayDate'] = DateTime::createFromFormat('Y-m-d', $array['displayDate']);
        
        return parent::convertArrayToObject($array);
    }


}