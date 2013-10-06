<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace DisplayItem;

use Traits\Database;

class Factory extends \Item\Factory
{
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
     * @param \DisplayItem\Entity $entity
     */
    public function deleteFromDatabase($entity)
    {
        $this->articleFactory->deleteFromDatabase($entity->getArticle());
        $this->productFactory->deleteFromDatabase($entity->getProduct());
    }

    /**
     * @param \DisplayItem\Entity $entity
     */
    public function persistToDatabase($entity)
    {
        $this->articleFactory->persistToDatabase($entity->getArticle());
        $this->productFactory->persistToDatabase($entity->getProduct());
    }

    protected function convertArrayToObject($array, $passThru = null)
    {
        $passThru['displayitemID'] = $this->getIDFromParts(
            [
                'articleID' => $array['articleID'],
                'productID' => $array['productID']
            ]
        );

        return parent::convertArrayToObject($array, $passThru);
    }
}