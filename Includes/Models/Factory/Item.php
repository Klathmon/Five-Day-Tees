<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use Exception;
use PDO;
use \Factory\Design;
use \Factory\Article;
use \Factory\Product;
use Settings;

/**
 * Class Item
 *
 * This class is a "Wrapper" for a few classes. It represents a distinct Design and all it's associated information (Articles and Products)
 * This is mainly used for displaying "previews" of all the shirts in our system (for example, the store page)
 */
class Item extends Design implements FactoryInterface
{
    use SQLTrait;
    
    /** @var Settings */
    protected $settings;
    /** @var Article */
    protected $articleFactory;
    /** @var Product */
    protected $productFactory;




    public function __construct(PDO $database, Settings $settings)
    {
        parent::__construct($database);
        $this->settings       = $settings;
        $this->articleFactory = new Article($database);
        $this->productFactory = new Product($database);
    }

    /**
     * @param int $designID
     *
     * @throws \Exception
     * @return \Object\Item
     */
    public function getByID($designID)
    {
        $sql = $this->selectItemSQL . $this->fromDesignSQL . 'WHERE Design.ID=:ID';

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':ID', $designID, PDO::PARAM_INT);
        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        if($result == false){
            throw new Exception('There are no items with that ID');
        }

        $array = $this->parseResults($result);

        return $this->convertArrayToObject(reset($array));

    }

    /**
     * @param string $name
     *
     * @throws \Exception
     * @return \Object\Item
     */
    public function getByName($name)
    {
        $sql = $this->selectItemSQL . $this->fromDesignSQL . 'WHERE Design.name = :name';

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if($result == false){
            throw new Exception('There are no items with that Name');
        }

        $array = $this->parseResults($result);

        return $this->convertArrayToObject(reset($array));
    }

    /**
     * @return \Object\Item[]
     */
    public function getAll()
    {
        $parsedArray = $this->parseResults($this->database->query($this->selectItemSQL . $this->fromDesignSQL . $this->orderByDisplayDateSQL)->fetchAll(PDO::FETCH_ASSOC));

        foreach ($parsedArray as $itemArray) {
            $items[] = $this->convertArrayToObject($itemArray);
        }

        return $items;
    }

    /**
     * @param int $start
     * @param int $limit
     *
     * @return \Object\Item[]
     */
    public function getQueue($start = 0, $limit = 100)
    {
        $statement = $this->database->prepare(
            $this->selectItemSQL . $this->fromDesignSQL . 'WHERE Design.displayDate >= :FutureDate ' . $this->orderByDisplayDateSQL . 'Limit :Start, :Amount'
        );

        $futureDate = $this->settings->getFeaturedDates()[3];

        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));
        $statement->bindValue('Start', $start, PDO::PARAM_INT);
        $statement->bindValue('Amount', $limit, PDO::PARAM_INT);

        $statement->execute();

        foreach ($this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC)) as $itemArray) {
            $items[] = $this->convertArrayToObject($itemArray);
        }

        return $items;
    }

    /**
     * @return \Object\Item[]
     */
    public function getFeatured()
    {
        $statement = $this->database->prepare($this->selectItemSQL . $this->fromDesignSQL . 'WHERE Design.displayDate > :PastDate AND Design.displayDate < :FutureDate' . $this->orderByDisplayDateSQL);

        $dates = $this->settings->getFeaturedDates();

        $pastDate   = $dates[0];
        $futureDate = $dates[3];


        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'));
        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));

        $statement->execute();

        foreach ($this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC)) as $itemArray) {
            $items[] = $this->convertArrayToObject($itemArray);
        }

        return $items;
    }

    /**
     * @param int $start
     * @param int $limit
     *
     * @return \Object\Item[]
     */
    public function getStore($start = 0, $limit = 50)
    {
        $query     = $this->selectItemSQL . $this->fromDesignSQL . $this->itemCountJoinSQL
            . 'WHERE Design.displayDate <= :PastDate AND itemsSold.totalSold < Design.salesLimit' . $this->orderByDisplayDateSQL . 'Limit :Start, :Amount';
        $statement = $this->database->prepare($query);

        $pastDate = $this->settings->getFeaturedDates()[0];

        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'));
        $statement->bindValue('Start', $start, PDO::PARAM_INT);
        $statement->bindValue('Amount', $limit, PDO::PARAM_INT);

        $statement->execute();

        foreach ($this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC)) as $itemArray) {
            $items[] = $this->convertArrayToObject($itemArray);
        }

        return $items;
    }

    /**
     * @param int $start
     * @param int $limit
     *
     * @return \Object\Item[]
     */
    public function getVault($start = 0, $limit = 50)
    {
        $query     = $this->selectItemSQL . $this->fromDesignSQL . $this->itemCountJoinSQL
            . 'WHERE itemsSold.totalSold >= Design.salesLimit' . $this->orderByDisplayDateSQL . 'Limit :Start, :Amount';
        $statement = $this->database->prepare($query);

        $statement->bindValue('Start', $start, PDO::PARAM_INT);
        $statement->bindValue('Amount', $limit, PDO::PARAM_INT);

        $statement->execute();

        foreach ($this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC)) as $itemArray) {
            $items[] = $this->convertArrayToObject($itemArray);
        }

        return $items;
    }

    public function persist($object)
    {

        foreach ($object->getArticles() as $article) {
            $this->articleFactory->persist($article);
        }

        foreach ($object->getProducts() as $product) {
            $this->productFactory->persist($product);
        }


        parent::persist($object);
    }

    /** Stub, don't use */
    public function create(){ }

    /** Stub, don't use */
    public function convertObjectToArray($object){ }

    public function convertArrayToObject($array)
    {
        $itemArray = $array['design'];
        
        foreach ($array['articles'] as $articleArray) {
            $itemArray['articles'][$articleArray['ID']] = $this->articleFactory->convertArrayToObject($articleArray);
        }

        foreach ($array['products'] as $productArray) {
            $itemArray['products'][$productArray['ID']] = $this->productFactory->convertArrayToObject($productArray);
        }
        
        return parent::convertArrayToObject($itemArray);
    }

    protected function parseResults($array)
    {
        $return = [];

        foreach ($array as $row) {
            $return[$row['designID']]['design']['ID']                                   = $row['designID'];
            $return[$row['designID']]['design']['name']                                 = $row['name'];
            $return[$row['designID']]['design']['displayDate']                          = $row['displayDate'];
            $return[$row['designID']]['design']['designImageURL']                       = $row['designImageURL'];
            $return[$row['designID']]['design']['salesLimit']                           = $row['salesLimit'];
            $return[$row['designID']]['design']['votes']                                = $row['votes'];
            $return[$row['designID']]['articles'][$row['articleID']]['ID']              = $row['articleID'];
            $return[$row['designID']]['articles'][$row['articleID']]['designID']        = $row['designID'];
            $return[$row['designID']]['articles'][$row['articleID']]['productID']       = $row['productID'];
            $return[$row['designID']]['articles'][$row['articleID']]['lastUpdated']     = $row['lastUpdated'];
            $return[$row['designID']]['articles'][$row['articleID']]['description']     = $row['description'];
            $return[$row['designID']]['articles'][$row['articleID']]['articleImageURL'] = $row['articleImageURL'];
            $return[$row['designID']]['articles'][$row['articleID']]['numberSold']      = $row['numberSold'];
            $return[$row['designID']]['articles'][$row['articleID']]['baseRetail']      = $row['baseRetail'];
            $return[$row['designID']]['products'][$row['productID']]['ID']              = $row['productID'];
            $return[$row['designID']]['products'][$row['productID']]['cost']            = $row['cost'];
            $return[$row['designID']]['products'][$row['productID']]['type']            = $row['type'];
            $return[$row['designID']]['products'][$row['productID']]['sizesAvailable']  = $row['sizesAvailable'];
        }

        return $return;
    }
}