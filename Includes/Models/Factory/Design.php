<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use DateTime;

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

    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);
        
        /** @var DateTime $displayDate */
        $displayDate = $array['displayDate'];
        $array['displayDate'] = $displayDate->format('Y-m-d');
        
        return $array;
    }

    public function convertArrayToObject($array)
    {
        $array['displayDate'] = DateTime::createFromFormat('Y-m-d', $array['displayDate']);
        
        return parent::convertArrayToObject($array);
    }


}