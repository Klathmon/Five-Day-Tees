<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Mapper;

use ConfigParser;
use PDO;
use \Mapper\Design;
use \Mapper\Article;
use \Mapper\Product;

/**
 * Class Item
 *
 * This class is a "Wrapper" for a few classes. It represents a distinct Design and all it's associated information (Articles and Products)
 * This is mainly used for displaying "previews" of all the shirts in our system (for example, the store page)
 */
class Item extends MapperBase implements MapperInterface
{
    /** @var ConfigParser */
    protected $config;
    /** @var Design */
    protected $designMapper;
    /** @var Article */
    protected $articleMapper;
    /** @var Product */
    protected $productMapper;

    private $queryStart
        = <<<SQL
SELECT 
    Design.ID AS designID, Design.name, Design.displayDate, Design.designImageURL, Design.salesLimit, Design.votes,
    Article.ID AS articleID, Article.lastUpdated, Article.description, Article.articleImageURL, Article.numberSold, Article.baseRetail,
    Product.ID AS productID, Product.cost, Product.type, Product.sizesAvailable
  FROM Design
    RIGHT JOIN Article ON (designID = Article.designID)
    LEFT JOIN Product ON (productID = Product.ID)
SQL;

    public function __construct(PDO $database, ConfigParser $config)
    {
        parent::__construct($database);
        $this->config        = $config;
        $this->designMapper  = new Design($database);
        $this->articleMapper = new Article($database);
        $this->productMapper = new Product($database);
    }

    /**
     * @param int $designID
     *
     * @return \Object\Item
     */
    public function getByID($designID)
    {
        $sql = $this->queryStart . ' WHERE Design.ID=:ID';

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':ID', $designID, PDO::PARAM_INT);
        $statement->execute();

        $array = $this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC));

        return $this->convertArrayToObject($array);

    }

    /**
     * @param $name
     *
     * @return \Object\Item
     */
    public function getByName($name)
    {
        $sql = $this->queryStart . ' WHERE Design.name = :name';

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->execute();

        $array = $this->parseResults($statement->fetchAll(PDO::FETCH_ASSOC));

        return $this->convertArrayToObject($array);
    }

    public function persist($object)
    {
        $this->designMapper->persist($object->getDesign());
        
        foreach($object->getArticles() as $article){
            $this->articleMapper->persist($article);
        }
        
        foreach($object->getProducts() as $product){
            $this->productMapper->persist($product);
        }
        
        unset($object);
    }

    /** Stub, don't use */
    public function create(){ }
    
    /** Stub, don't use */
    protected function convertObjectToArray($object){ }

    protected function convertArrayToObject($array)
    {
        $design = $this->designMapper->convertArrayToObject($array['design']);
        
        foreach($array['articles'] as $articleArray){
            $articles[$articleArray['ID']] = $this->articleMapper->convertArrayToObject($articleArray);
        }
        
        foreach($array['products'] as $productArray){
            $products[$productArray['ID']] = $this->productMapper->convertArrayToObject($productArray);
        }
        
        return parent::convertArrayToObject(['design' => $design, 'articles' => $articles, 'products' => $products]);
    }

    private function parseResults($array)
    {
        $return = [];

        foreach ($array as $row) {
            $return['design']['ID']                                   = $row['designID'];
            $return['design']['name']                                 = $row['name'];
            $return['design']['displayDate']                          = $row['displayDate'];
            $return['design']['designImageURL']                       = $row['designImageURL'];
            $return['design']['salesLimit']                           = $row['salesLimit'];
            $return['design']['votes']                                = $row['votes'];
            $return['articles'][$row['articleID']]['ID']              = $row['articleID'];
            $return['articles'][$row['articleID']]['designID']        = $row['designID'];
            $return['articles'][$row['articleID']]['productID']       = $row['productID'];
            $return['articles'][$row['articleID']]['lastUpdated']     = $row['lastUpdated'];
            $return['articles'][$row['articleID']]['description']     = $row['description'];
            $return['articles'][$row['articleID']]['articleImageURL'] = $row['articleImageURL'];
            $return['articles'][$row['articleID']]['numberSold']      = $row['numberSold'];
            $return['articles'][$row['articleID']]['baseRetail']      = $row['baseRetail'];
            $return['products'][$row['productID']]['ID']              = $row['productID'];
            $return['products'][$row['productID']]['cost']            = $row['cost'];
            $return['products'][$row['productID']]['type']            = $row['type'];
            $return['products'][$row['productID']]['sizesAvailable']  = $row['sizesAvailable'];
        }

        return $return;
    }
}