<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

namespace Mapper;

use PDO, \Exception, \DateTime, \ConfigParser, \Settings;

/**
 * Class Item
 *
 * This is the Mapper class for Items.
 */
class Item implements Mapper
{
    /**
     * @var PDO The database connection
     */
    private $database;
    /**
     * @var ConfigParser The Configuration Parser
     */
    private $config;
    /**
     * @var Settings The Settings Handler
     */
    private $settings;


    /**
     * Pass the $database connection in at construction.
     *
     * @param PDO          $database
     * @param ConfigParser $config
     */
    public function __construct(PDO $database, ConfigParser $config)
    {
        $this->database = $database;
        $this->config   = $config;
        $this->settings = new Settings($database, $config);
    }

    /**
     * Returns a single Item by it's ID
     *
     * @param int $ID
     *
     * @return \Entity\Item
     */
    public function getByID($ID)
    {
        $sql
            = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE Items.ID = :ID
  LIMIT 1
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':ID', $ID, PDO::PARAM_INT);

        $statement->execute();

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Returns items by their Name
     *
     * @param string $name
     *
     * @return \Entity\Item[]
     */
    public function getByName($name)
    {
        $sql
            = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE Items.Name = :Name
  ORDER BY ItemsCommon.DisplayDate DESC, Items.Gender ASC
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':Name', $name, PDO::PARAM_STR);

        $statement->execute();

        $array = false;

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $array[] = $this->createEntity($item);
        }

        return $array;
    }

    /**
     * Returns an array of all Items in the table
     *
     * @param int $start
     * @param int $limit
     *
     * @return \Entity\Item[]
     */
    public function listAll($start = 0, $limit = 500)
    {

        $sql
            = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  ORDER BY ItemsCommon.DisplayDate DESC, Items.Gender ASC 
  LIMIT :Start, :Amount
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':Start', $start, PDO::PARAM_INT);
        $statement->bindValue(':Amount', $limit, PDO::PARAM_INT);

        $statement->execute();


        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $array[] = $this->createEntity($item);
        }

        return $array;
    }

    /**
     * Gets the shirts that are queue'd up to be shown at a later date
     *
     * @param bool $preview If true, it will only return one of each shirt (by name)
     * @param int  $start
     * @param int  $limit
     *
     * @return \Entity\Item[]
     */
    public function getQueue($preview = false, $start = 0, $limit = 100)
    {

        if ($preview) {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, MAX(Items.Gender) AS Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE DisplayDate >= :FutureDate
  GROUP BY Items.Name
  ORDER BY ItemsCommon.DisplayDate DESC
  LIMIT :Start, :Amount
SQL;
        } else {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE DisplayDate >= :FutureDate
  ORDER BY ItemsCommon.DisplayDate DESC, Items.Gender ASC 
  LIMIT :Start, :Amount
SQL;
        }

        $statement = $this->database->prepare($sql);

        $futureDate = $this->settings->getFeaturedDates()[3];

        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));
        $statement->bindValue('Start', $start, PDO::PARAM_INT);
        $statement->bindValue('Amount', $limit, PDO::PARAM_INT);

        $statement->execute();

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $array[] = $this->createEntity($item);
        }

        return $array;
    }

    /**
     * Gets the shirts that should appear on the Featured page
     *
     * @param bool $preview If true, it will only return one of each shirt (by name)
     *
     * @return \Entity\Item[]
     */
    public function getFeatured($preview = false)
    {

        if ($preview) {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, MAX(Items.Gender) AS Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE DisplayDate > :PastDate 
    AND DisplayDate < :FutureDate
  GROUP BY Items.Name
  ORDER BY ItemsCommon.DisplayDate DESC
SQL;
        } else {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE DisplayDate > :PastDate 
    AND DisplayDate < :FutureDate
  ORDER BY ItemsCommon.DisplayDate DESC, Items.Gender ASC
SQL;
        }

        $statement = $this->database->prepare($sql);

        $dates = $this->settings->getFeaturedDates();

        $pastDate   = $dates[0];
        $futureDate = $dates[3];


        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'));
        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));

        $statement->execute();

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $array[] = $this->createEntity($item);
        }

        return $array;
    }

    /**
     * Gets the shirts that are in the "Store"
     *
     * @param bool $preview If true, it will only return one of each shirt (by name)
     * @param int  $start   The shirt to start with (for pagination)
     * @param int  $limit   The max amount to return (for pagination)
     *
     * @return \Entity\Item[]
     */
    public function getStore($preview = false, $start = 0, $limit = 50)
    {

        if ($preview) {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, MAX(Items.Gender) AS Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE DisplayDate <= :PastDate
    AND ItemsSold.TotalSold < ItemsCommon.SalesLimit
  GROUP BY Items.Name
  ORDER BY ItemsCommon.DisplayDate DESC
  LIMIT :Start, :Amount
SQL;
        } else {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE DisplayDate <= :PastDate
    AND ItemsSold.TotalSold < ItemsCommon.SalesLimit
  ORDER BY ItemsCommon.DisplayDate DESC, Items.Gender ASC
  LIMIT :Start, :Amount
SQL;
        }

        $statement = $this->database->prepare($sql);

        $pastDate = $this->settings->getFeaturedDates()[0];

        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'), PDO::PARAM_STR);
        $statement->bindValue('Start', $start, PDO::PARAM_INT);
        $statement->bindValue('Amount', $limit, PDO::PARAM_INT);

        $statement->execute();

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $array[] = $this->createEntity($item);
        }

        return $array;
    }

    /**
     * Gets the shirts that are in the "Vault"
     *
     * @param bool $preview If true, it will only return one of each shirt (by name)
     * @param int  $start   The shirt to start with (for pagination)
     * @param int  $limit   The max amount to return (for pagination)
     *
     * @return \Entity\Item[]
     */
    public function getVault($preview = false, $start = 0, $limit = 50)
    {

        if ($preview) {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, MAX(Items.Gender) AS Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE ItemsSold.TotalSold >= ItemsCommon.SalesLimit
  GROUP BY Items.Name
  ORDER BY ItemsCommon.DisplayDate DESC
  LIMIT :Start, :Amount
SQL;
        } else {
            $sql
                = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE ItemsSold.TotalSold >= ItemsCommon.SalesLimit
  ORDER BY ItemsCommon.DisplayDate DESC, Items.Gender ASC
  LIMIT :Start, :Amount
SQL;
        }

        $statement = $this->database->prepare($sql);

        $statement->bindValue('Start', $start, PDO::PARAM_INT);
        $statement->bindValue('Amount', $limit, PDO::PARAM_INT);

        $statement->execute();

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $array[] = $this->createEntity($item);
        }

        return $array;
    }

    /**
     * Returns an item based on it's name and gender
     *
     * @param string $name
     * @param string $gender
     *
     * @return \Entity\Item
     */
    public function getItemByNameGender($name, $gender)
    {

        $sql
            = <<<SQL
SELECT Items.ID, Items.Name, Items.Gender, Items.ArticleID, Items.DesignID, Items.Description, Items.Cost, Items.Retail, Items.ProductImage, 
    Items.DesignImage, Items.SizesAvailable, Items.LastUpdated, Items.Sold, ItemsCommon.SalesLimit, ItemsCommon.DisplayDate, ItemsCommon.Votes,
    ItemsSold.TotalSold
  FROM Items 
  LEFT JOIN ItemsCommon ON (Items.Name = ItemsCommon.Name)
  LEFT JOIN (SELECT Name, SUM(Sold) AS TotalSold FROM Items GROUP BY Name) AS ItemsSold ON Items.Name = ItemsSold.Name
  WHERE Items.Name = :Name AND Items.Gender = :Gender
  LIMIT 1
SQL;

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':Name', $name, PDO::PARAM_STR);
        $statement->bindValue(':Gender', strtolower($gender), PDO::PARAM_STR);

        $statement->execute();

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Returns an array of the ItemsCommon data for the given Name
     *
     * @param $name
     *
     * @return \Entity\Item
     */
    public function getItemsCommonByName($name)
    {
        $sql = 'SELECT DisplayDate, SalesLimit, Votes FROM ItemsCommon WHERE Name=:Name LIMIT 1';

        $statement = $this->database->prepare($sql);

        $statement->bindValue(':Name', $name, PDO::PARAM_STR);

        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return [false, false, false];
        } else {
            return [$row['SalesLimit'], DateTime::createFromFormat('Y-m-d', $row['DisplayDate']), $row['Votes']];
        }
    }

    /**
     * Returns the last date used in the database, if no date exists, it uses the default from the Settings class.
     *
     * @return DateTime
     */
    public function getLastDate()
    {
        $sql = 'SELECT MAX(DisplayDate) FROM ItemsCommon';

        $statement = $this->database->query($sql);

        $result = $statement->fetch(PDO::FETCH_NUM)[0];

        if (is_null($result)) {
            return $this->settings->getStartingDisplayDate();
        } else {
            return DateTime::createFromFormat('Y-m-d', $result);
        }
    }


    /**
     * Delete an Item from the database.
     *
     * @param \Entity\Item $entity
     *
     * @throws \Exception
     */
    public function delete($entity)
    {
        if (empty($entity)) {
            //Whoops, forgot the $entity
            throw new Exception('ERROR! No Entity given for delete statement');
        }

        $statement = $this->database->prepare('DELETE FROM Items WHERE `ID` = :ID LIMIT 1');
        $statement->bindValue(':ID', $entity->getID());
        $statement->execute();

        /* Delete the associated ItemsCommon row if there are no more Items with that name */
        if (!$this->nameExistsInItems($entity->getName())) {
            $statement = $this->database->prepare('DELETE FROM ItemsCommon WHERE Name = :Name LIMIT 1');
            $statement->bindValue(':Name', $entity->getName());
            $statement->execute();
        }
    }

    /**
     * Persists an Item to the database, will UPDATE if one with that ID exists, will INSERT if not.
     *
     * @param \Entity\Item $entity
     *
     * @throws \Exception
     */
    public function persist($entity)
    {
        try{

            /* Start the transaction */
            $this->database->beginTransaction();

            /* This will SELECT the Name from the Items database to compare and see if it changed */
            $itemsDeleteCheck = $this->database->prepare(
                <<<SQL
SELECT Name FROM Items WHERE ID=:ID
SQL
            );

            /* This will INSERT the Item's Common info into the database if it does not exist, and will UPDATE it if it does */
            $itemsCommon = $this->database->prepare(
                <<<SQL
INSERT INTO ItemsCommon
  SET
    Name=:Name,
    SalesLimit=:SalesLimit,
    DisplayDate=:DisplayDate,
    Votes=:Votes
  ON DUPLICATE KEY UPDATE
    SalesLimit=:SalesLimit,
    DisplayDate=:DisplayDate,
    Votes=:Votes
SQL
            );

            /* This will INSERT the item into the database if it does not exist, and will UPDATE it if it does */
            $items = $this->database->prepare(
                <<<SQL
INSERT INTO Items
  SET
    `ID`=:ID,
    Name=:Name,
    Gender=:Gender,
    ArticleID=:ArticleID,
    DesignID=:DesignID,
    Description=:Description,
    Cost=:Cost,
    Retail=:Retail,
    ProductImage=:ProductImage,
    DesignImage=:DesignImage,
    SizesAvailable=:SizesAvailable,
    LastUpdated=:LastUpdated,
    Sold=:Sold
  ON DUPLICATE KEY UPDATE
    Name=:Name,
    Gender=:Gender,
    ArticleID=:ArticleID,
    DesignID=:DesignID,
    Description=:Description,
    Cost=:Cost,
    Retail=:Retail,
    ProductImage=:ProductImage,
    DesignImage=:DesignImage,
    SizesAvailable=:SizesAvailable,
    LastUpdated=:LastUpdated,
    Sold=:Sold
SQL
            );


            $itemsDeleteCheck->bindValue(':ID', $entity->getID(), PDO::PARAM_INT);

            $itemsCommon->bindValue(':Name', $entity->getName(), PDO::PARAM_STR);
            $itemsCommon->bindValue(':SalesLimit', $entity->getSalesLimit(), PDO::PARAM_INT);
            $itemsCommon->bindValue(':DisplayDate', $entity->getDisplayDate()->format('Y-m-d'), PDO::PARAM_STR);
            $itemsCommon->bindValue(':Votes', $entity->getVotes(), PDO::PARAM_INT);

            $items->bindValue(':ID', $entity->getID(), PDO::PARAM_INT);
            $items->bindValue(':Name', $entity->getName(), PDO::PARAM_STR);
            $items->bindValue(':Gender', $entity->getGender(), PDO::PARAM_STR);
            $items->bindValue(':ArticleID', $entity->getArticleID(), PDO::PARAM_STR);
            $items->bindValue(':DesignID', $entity->getDesignID(), PDO::PARAM_STR);
            $items->bindValue(':Description', $entity->getDescription(), PDO::PARAM_STR);
            $items->bindValue(':Cost', $entity->getCost(), PDO::PARAM_STR);
            $items->bindValue(':Retail', $entity->getRetail(), PDO::PARAM_STR);
            $items->bindValue(':ProductImage', $entity->getProductImage(), PDO::PARAM_STR);
            $items->bindValue(':DesignImage', $entity->getDesignImage(), PDO::PARAM_STR);
            $items->bindValue(':SizesAvailable', implode(',', $entity->getSizesAvailable()), PDO::PARAM_STR);
            $items->bindValue(':LastUpdated', $entity->getLastUpdated()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $items->bindValue(':Sold', $entity->getNumberSold(), PDO::PARAM_INT);

            /* Execute the SELECT/INSERT statements */
            $itemsDeleteCheck->execute();
            $itemsCommon->execute();
            $items->execute();

            $oldName = $itemsDeleteCheck->fetch(PDO::FETCH_ASSOC)['Name'];

            if ($oldName != $entity->getName()) {
                //The name changed, delete the old (now orphaned) Items Common row

                $itemsCommonDelete = $this->database->prepare(
                    <<<SQL
    DELETE FROM ItemsCommon WHERE Name=:OldName
SQL
                );

                $itemsCommonDelete->bindValue(':OldName', $oldName, PDO::PARAM_STR);

                $itemsCommonDelete->execute();

            }


        } catch(Exception $e){
            /* H0LY SHIT! Something went wrong, so roll back changes and toss the Exception back up the ladder */
            $this->database->rollBack();
            throw $e;
        }

        /* Everything's Good, commit this to the database and free it up for other things */
        $this->database->commit();

        /* Because the ID is created when it is persisted, unset the entity after it is persisted to avoid any other changes that won't be saved */
        unset($entity);
    }

    /**
     * Create a new entity out of the data
     *
     * @param array $data The array of data to fill the object with
     *
     * @return \Entity\Item
     */
    private function createEntity($data)
    {
        if ($data === false) {
            $entity = false;
        } else {
            $entity = new \Entity\Item($data['ID']);
            $entity->setName($data['Name']);
            $entity->setGender($data['Gender']);
            $entity->setArticleID($data['ArticleID']);
            $entity->setDesignID($data['DesignID']);
            $entity->setDescription($data['Description']);
            $entity->setSalesLimit($data['SalesLimit']);
            $entity->setDisplayDate(DateTime::createFromFormat('Y-m-d', $data['DisplayDate']));
            $entity->setCost($data['Cost']);
            $entity->setRetail($data['Retail']);
            $entity->setProductImage($data['ProductImage']);
            $entity->setDesignImage($data['DesignImage']);
            $entity->setSizesAvailable(explode(',', $data['SizesAvailable']));
            $entity->setLastUpdated(DateTime::createFromFormat('Y-m-d H:i:s', $data['LastUpdated']));
            $entity->setNumberSold($data['Sold']);
            $entity->setVotes($data['Votes']);
            $entity->setTotalSold($data['TotalSold']);
            $entity->setCategory($this->settings->getItemCategory($entity));
        }

        return $entity;
    }

    /**
     * Checks to see if there is any other Items with that name. This is to see if it is safe to delete the associated name in the ItemsCommon table
     *
     * @param string $name
     *
     * @return bool
     */
    private function nameExistsInItems($name)
    {
        $statement = $this->database->prepare('SELECT EXISTS(SELECT 1 FROM Items WHERE `Name`=:Name LIMIT 1)');
        $statement->bindValue(':Name', $name, PDO::PARAM_STR);
        $statement->execute();

        return ($statement->fetch(PDO::FETCH_NUM)[0] == 0 ? false : true);
    }
}