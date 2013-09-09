<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use DateTime;

class Article extends FactoryBase implements FactoryInterface
{

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param int $ID
     * @param int $designID
     * @param int $productID
     * @param DateTime $lastUpdated
     * @param string $description
     * @param string $articleImageURL
     * @param int $numberSold
     *
     * @return \Object\Article
     */
    public function create(
        $ID = null,
        $designID = null,
        $productID = null,
        $lastUpdated = null,
        $description = null,
        $articleImageURL = null,
        $numberSold = null,
        $baseRetail = null
    ){
        $array = [
            'ID' => $ID,
            'designID' => $designID,
            'productID' => $productID,
            'lastUpdated' => $lastUpdated,
            'description' => $description,
            'articleImageURL' => $articleImageURL,
            'numberSold' => $numberSold,
            'baseRetail' => $baseRetail
        ];
        
        return parent::convertArrayToObject($array);
    }

    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);
        
        /** @var DateTime $lastUpdated */
        $lastUpdated = $array['lastUpdated'];
        $array['lastUpdated'] = $lastUpdated->format('Y-m-d H:i:s');
        
        return $array;
    }

    public function convertArrayToObject($array)
    {
        $array['lastUpdated'] = DateTime::createFromFormat('Y-m-d H:i:s', $array['lastUpdated']);
        
        return parent::convertArrayToObject($array);
    }

}