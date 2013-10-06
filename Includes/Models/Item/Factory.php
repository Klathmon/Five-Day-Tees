<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Item;

use Exception;
use Interfaces\Item;
use ReflectionClass;
use ReflectionProperty;
use Traits\Database;

abstract class Factory extends \Abstracts\Factory implements Item
{
    use Database;

    /** @var \Settings */
    protected $settings;
    /** @var \Article\Factory */
    protected $articleFactory;
    /** @var \Product\Factory */
    protected $productFactory;

    protected $IDSeperator = '-';

    protected $sqlSelect = <<<SQL
SELECT Article.*, Product.*, itemsSold.totalSold 
    FROM Article 
    LEFT JOIN Product USING (articleID) 
    LEFT JOIN (
            SELECT 
                articleID, SUM(sold) AS totalSold
              FROM Product
              GROUP BY articleID
          ) AS itemsSold USING (articleID)
SQL;

    public function __construct($database, $settings)
    {
        parent::__construct($database);

        $this->settings       = $settings;
        $this->articleFactory = new \Article\Factory($this->database, $this->settings);
        $this->productFactory = new \Product\Factory($this->database, $this->settings);
    }

    public function getByItemIDFromDatabase($ID)
    {
        $statement = $this->database->prepare(
            $this->sqlSelect . ' WHERE articleID=:articleID AND productID =:productID LIMIT 1'
        );

        $IDs = $this->getPartsFromItemID($ID);

        $statement->bindValue(':articleID', $IDs['articleID']);
        $statement->bindValue(':productID', $IDs['productID']);

        $statement->execute();

        return $this->executeAndParse($statement)[0];
    }

    /**
     * @param string $name
     *
     * @return \Item\Entity[]
     */
    public function getByNameFromDatabase($name)
    {
        $statement = $this->database->prepare(
            $this->sqlSelect . ' WHERE name=:name'
        );

        $statement->bindValue(':name', $name);

        $statement->execute();

        return $this->executeAndParse($statement);
    }

    public function getAllFromDatabase()
    {
        $statement = $this->database->prepare($this->sqlSelect);

        return $this->executeAndParse($statement);
    }

    public function deleteFromDatabase($entity){ }

    public function persistToDatabase($entity){ }

    public function getItemIDFromParts($array)
    {
        return implode(
            $this->IDSeperator,
            [
                $array['articleID'],
                $array['productID']
            ]
        );
    }

    public function getPartsFromItemID($ID)
    {
        $array = explode($this->IDSeperator, $ID);

        return [
            'articleID' => $array[0],
            'productID' => $array[1]
        ];
    }

    final protected function convertObjectToArray($entity)
    {
        throw new Exception('Can\'t convert Items (or their derivitives) to Arrays');
    }

    protected function convertArrayToObject($array, $passThru = [])
    {
        $temp['itemID']       = $this->getItemIDFromParts(
            [
                'articleID' => $array['articleID'],
                'productID' => $array['productID']
            ]
        );
        $temp['article']      = $this->articleFactory->createFromData($array);
        $temp['product']      = $this->productFactory->createFromData($array);
        $temp['totalSold']    = $array['totalSold'];
        $temp['category']     = $this->settings->getItemCategory($temp['article']->getDate(), $temp['totalSold'], $temp['article']->getSalesLimit());
        $temp['currentPrice'] = $this->settings->getItemCurrentPrice($temp['product']->getRetail(), $temp['category']);
        
        
        $temp = array_merge($temp, $passThru);

        return parent::convertArrayToObject($temp);
    }


}