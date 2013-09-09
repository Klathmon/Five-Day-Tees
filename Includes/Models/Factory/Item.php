<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

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
class Item extends FactoryBase implements FactoryInterface
{
    /** @var Settings */
    protected $settings;
    /** @var Design */
    protected $designFactory;
    /** @var Article */
    protected $articleFactory;
    /** @var Product */
    protected $productFactory;

    private $queryStart
        = <<<SQL
SELECT 
    Design.ID AS designID, Design.name, Design.displayDate, Design.designImageURL, Design.salesLimit, Design.votes,
    Article.ID AS articleID, Article.lastUpdated, Article.description, Article.articleImageURL, Article.numberSold, Article.baseRetail,
    Product.ID AS productID, Product.cost, Product.type, Product.sizesAvailable
  FROM Design
    LEFT JOIN Article ON (Design.ID = Article.designID)
    LEFT JOIN Product ON (Article.productID = Product.ID)
SQL;

    private $itemCountJoin
        = <<<SQL
LEFT JOIN (
    SELECT designID, SUM(numberSold) AS totalSold 
        FROM Article 
        GROUP BY designID
    ) AS itemsSold ON (Design.ID = itemsSold.designID)
SQL;


    public function __construct(PDO $database, Settings $settings)
    {
        parent::__construct($database);
        $this->settings       = $settings;
        $this->designFactory  = new Design($database);
        $this->articleFactory = new Article($database);
        $this->productFactory = new Product($database);
    }

    /**
     * @param int $designID
     *
     * @return \Object\Item
     */
    public function getByID($designID)
    {
        $sql = $this->queryStart . 'WHERE Design.ID=:ID';

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':ID', $designID, PDO::PARAM_INT);
        $statement->execute();

        $array = $this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC));

        return $this->convertArrayToObject(reset($array));

    }

    /**
     * @param $name
     *
     * @return \Object\Item
     */
    public function getByName($name)
    {
        $sql = $this->queryStart . 'WHERE Design.name = :name';

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->execute();

        $array = $this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC));

        return $this->convertArrayToObject(reset($array));
    }

    public function getAll()
    {
        $parsedArray = $this->parseResults($this->database->query($this->queryStart)->fetchAll(PDO::FETCH_ASSOC));

        foreach ($parsedArray as $itemArray) {
            $items[] = $this->convertArrayToObject($itemArray);
        }

        return $items;
    }

    public function getQueue($start = 0, $limit = 100)
    {
        $statement = $this->database->prepare($this->queryStart . 'WHERE Design.displayDate >= :FutureDate Limit :Start, :Amount');

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

    public function getFeatured()
    {
        $statement = $this->database->prepare($this->queryStart . 'WHERE Design.displayDate > :PastDate AND Design.displayDate < :FutureDate');

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

    public function getStore($start = 0, $limit = 50)
    {
        $query     = $this->queryStart . $this->itemCountJoin
            . 'WHERE Design.displayDate <= :PastDate AND itemsSold.totalSold < Design.salesLimit Limit :Start, :Amount';
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

    public function getVault($start = 0, $limit = 50)
    {
        $query     = $this->queryStart . $this->itemCountJoin
            . 'WHERE itemsSold.totalSold >= Design.salesLimit Limit :Start, :Amount';
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
        $this->designFactory->persist($object->getDesign());

        foreach ($object->getArticles() as $article) {
            $this->articleFactory->persist($article);
        }

        foreach ($object->getProducts() as $product) {
            $this->productFactory->persist($product);
        }

        unset($object);
    }

    /** Stub, don't use */
    public function create(){ }

    /** Stub, don't use */
    protected function convertObjectToArray($object){ }

    protected function convertArrayToObject($array)
    {
        $design = $this->designFactory->convertArrayToObject($array['design']);

        foreach ($array['articles'] as $articleArray) {
            $articles[$articleArray['ID']] = $this->articleFactory->convertArrayToObject($articleArray);
        }

        foreach ($array['products'] as $productArray) {
            $products[$productArray['ID']] = $this->productFactory->convertArrayToObject($productArray);
        }

        return parent::convertArrayToObject(['design' => $design, 'articles' => $articles, 'products' => $products]);
    }

    private function parseResults($array)
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