<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Item;

use Interfaces\Item;
use Traits\Database;

class Factory extends \Abstracts\Factory implements Item
{
    use Database;

    /** @var \Settings */
    protected $settings;
    /** @var \Article\Factory */
    protected $articleFactory;
    /** @var \Product\Factory */
    protected $productFactory;

    private $sqlSelect = <<<SQL
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

    public function getByIDFromDatabase($ID)
    {
        $statement = $this->database->prepare(
            $this->sqlSelect . ' WHERE articleID=:articleID AND productID =:productID LIMIT 1'
        );

        $IDs = $this->getPartsFromID($ID);

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

    public function getQueueFromDatabase()
    {
        $statement = $this->database->prepare($this->sqlSelect . ' WHERE date >= :FutureDate GROUP BY Article.articleID ORDER BY date DESC');

        $futureDate = $this->settings->getFeaturedDates()[3];

        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));

        return $this->executeAndParse($statement);
    }

    public function getFeaturedFromDatabase()
    {
        $statement = $this->database->prepare($this->sqlSelect . ' WHERE date > :PastDate AND date < :FutureDate GROUP BY Article.articleID ORDER BY date DESC');

        $dates = $this->settings->getFeaturedDates();

        $pastDate   = $dates[0];
        $futureDate = $dates[3];

        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'));
        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));

        return $this->executeAndParse($statement);
    }

    public function getStoreFromDatabase()
    {
        $statement = $this->database->prepare($this->sqlSelect . ' WHERE date <= :PastDate AND totalSold < salesLimit GROUP BY Article.articleID ORDER BY date DESC');

        $pastDate = $this->settings->getFeaturedDates()[0];

        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'));

        return $this->executeAndParse($statement);
    }

    public function getVaultFromDatabase()
    {
        $statement = $this->database->prepare($this->sqlSelect . ' WHERE totalSold >= salesLimit GROUP BY Article.articleID ORDER BY date DESC');

        return $this->executeAndParse($statement);
    }

    /**
     * @param \Item\Entity $entity
     */
    public function deleteFromDatabase($entity)
    {
        $this->articleFactory->deleteFromDatabase($entity->getArticle());
        $this->productFactory->deleteFromDatabase($entity->getProduct());
    }

    /**
     * @param \Item\Entity $entity
     */
    public function persistToDatabase($entity)
    {
        $this->articleFactory->persistToDatabase($entity->getArticle());
        $this->productFactory->persistToDatabase($entity->getProduct());
    }

    public function getIDFromParts($array)
    {
        return implode(
            '-',
            [
                $array['articleID'],
                $array['productID']
            ]
        );
    }

    public function getPartsFromID($ID)
    {
        $array = explode('-', $ID);

        return [
            'articleID' => $array[0],
            'productID' => $array[1]
        ];
    }

    protected function parseDatabaseResult($array)
    {
        foreach ($array as $row) {
            $temp['itemID']       = $this->getIDFromParts(
                [
                    'articleID' => $row['articleID'],
                    'productID' => $row['productID']
                ]
            );
            $temp['article']      = $this->articleFactory->createFromData($row);
            $temp['product']      = $this->productFactory->createFromData($row);
            $temp['totalSold']    = $row['totalSold'];
            $temp['category']     = $this->settings->getItemCategory($temp['article']->getDate(), $temp['totalSold'], $temp['article']->getSalesLimit());
            $temp['currentPrice'] = $this->settings->getItemCurrentPrice($temp['product']->getRetail(), $temp['category']);

            $returnArray[] = $this->convertArrayToObject($temp);
        }

        return $returnArray;
    }
}