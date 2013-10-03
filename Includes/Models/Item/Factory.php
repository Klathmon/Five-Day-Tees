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

    /** @var \Article\Factory */
    protected $articleFactory;
    /** @var \Product\Factory */
    protected $productFactory;

    private $sqlSelect = 'SELECT * FROM Article LEFT JOIN Product USING (articleID) ';

    public function __construct($database, $settings)
    {
        parent::__construct($database);

        $this->articleFactory = new \Article\Factory($database, $settings);
        $this->productFactory = new \Product\Factory($database, $settings);
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

    public function getAllFromDatabase()
    {
        $statement = $this->database->prepare($this->sqlSelect);

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
            '|',
            [
                $array['articleID'],
                $array['productID']
            ]
        );
    }

    public function getPartsFromID($ID)
    {
        $array = explode('|', $ID);

        return [
            'articleID' => $array[0],
            'productID' => $array[1]
        ];
    }

    protected function parseDatabaseResult($array)
    {
        foreach ($array as $row) {
            $returnArray[] = $this->convertArrayToObject([
                'itemID' => $this->getIDFromParts(['articleID' => $row['articleID'], 'productID' => $row['productID']]),
                'article' => $this->articleFactory->createFromData($row),
                'product' => $this->productFactory->createFromData($row)
            ]);
        }

        return $returnArray;
    }
}