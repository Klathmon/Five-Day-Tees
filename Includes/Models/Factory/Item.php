<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

use Exception;
use Settings;
use PDO;
use \Factory\Design;
use \Factory\Article;
use \Factory\Product;
use Traits\SQLStatements;

class Item extends FactoryBase implements FactoryInterface
{

    use SQLStatements;

    /** @var Settings */
    protected $settings;
    /** @var Article */
    protected $articleFactory;
    /** @var Product */
    protected $productFactory;
    /** @var Design */
    protected $designFactory;


    public function __construct(PDO $database, Settings $settings)
    {
        parent::__construct($database);
        $this->settings       = $settings;
        $this->articleFactory = new Article($database);
        $this->productFactory = new Product($database);
        $this->designFactory  = new Design($database);
    }

    /**
     * @param string $ID
     *
     * @throws \Exception
     * @return \Object\Item
     */
    public function getByID($ID)
    {
        list($articleID, $productID, $designID, $size) = $this->getPartsFromID($ID);

        $sql = $this->SQLItemSelect . <<<SQL
WHERE articleID=:articleID 
  AND productID=:productID 
  AND designID=:designID 
  AND size=:size
LIMIT 1
SQL;

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':articleID', $articleID, PDO::PARAM_STR);
        $statement->bindValue(':productID', $productID, PDO::PARAM_STR);
        $statement->bindValue(':designID', $designID, PDO::PARAM_INT);
        $statement->bindValue(':size', $size, PDO::PARAM_STR);

        return $this->executeAndParse($statement);

    }

    /**
     * @param string $name
     *
     * @throws \Exception
     * @return \Object\Item[]
     */
    public function getByName($name)
    {
        $sql = $this->SQLItemSelect . 'WHERE name=:name';

        $statement = $this->database->prepare($sql);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);

        return $this->executeAndParse($statement);
    }

    /**
     * @param bool $preview **NOTE** preview acts differently here, it displays all distinct articles (ignoring copies for each size)
     *
     * @return \Object\Item[]
     */
    public function getAll($preview = true)
    {
        $sql = $this->SQLItemSelect;
        if ($preview) {
            $sql .= ' GROUP BY Article.articleID';
        }
        $sql .= $this->SQLItemSelectSuffix;

        $statement = $this->database->prepare($sql);

        return $this->executeAndParse($statement);
    }

    /**
     * @param bool $preview
     *
     * @return \Object\Item[]
     */
    public function getQueue($preview = true)
    {
        $sql = $this->SQLItemSelect . 'WHERE displayDate >= :FutureDate';
        if ($preview) {
            $sql .= ' GROUP BY Design.designID';
        }
        $sql .= $this->SQLItemSelectSuffix;
        $statement = $this->database->prepare($sql);

        $futureDate = $this->settings->getFeaturedDates()[3];

        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));

        return $this->executeAndParse($statement);
    }

    /**
     * @param bool $preview if true, it will only return one of each design
     *
     * @return \Object\Item[]
     */
    public function getFeatured($preview = true)
    {
        $sql = $this->SQLItemSelect . 'WHERE displayDate > :PastDate AND displayDate < :FutureDate';
        if ($preview) {
            $sql .= ' GROUP BY Design.designID';
        }
        $sql .= $this->SQLItemSelectSuffix;
        $statement = $this->database->prepare($sql);

        $dates = $this->settings->getFeaturedDates();

        $pastDate   = $dates[0];
        $futureDate = $dates[3];


        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'));
        $statement->bindValue('FutureDate', $futureDate->format('Y-m-d'));

        return $this->executeAndParse($statement);
    }

    /**
     * @param bool $preview
     *
     * @return \Object\Item[]
     */
    public function getStore($preview = true)
    {

        $sql = $this->SQLItemSelect . 'WHERE displayDate <= :PastDate AND totalSold < salesLimit';
        if ($preview) {
            $sql .= ' GROUP BY Design.designID';
        }
        $sql .= $this->SQLItemSelectSuffix;
        $statement = $this->database->prepare($sql);

        $pastDate = $this->settings->getFeaturedDates()[0];

        $statement->bindValue('PastDate', $pastDate->format('Y-m-d'));

        return $this->executeAndParse($statement);
    }

    /**
     * @param bool $preview
     *
     * @return \Object\Item[]
     */
    public function getVault($preview = true)
    {
        $sql = $this->SQLItemSelect . 'WHERE totalSold >= salesLimit';
        if ($preview) {
            $sql .= ' GROUP BY Design.designID';
        }
        $sql .= $this->SQLItemSelectSuffix;
        $statement = $this->database->prepare($sql);

        return $this->executeAndParse($statement);
    }

    public function save($array)
    {
        $this->articleFactory->save($array['article']);
        $this->productFactory->save($array['product']);
        $this->designFactory->save($array['design']);
    }

    public function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);

        $array['article'] = $this->articleFactory->convertObjectToArray($array['article']);
        $array['product'] = $this->productFactory->convertObjectToArray($array['product']);
        $array['design']  = $this->designFactory->convertObjectToArray($array['design']);

        return $array;
    }

    public function convertArrayToObject($array)
    {
        $returnArray['article']   = $this->articleFactory->create($array);
        $returnArray['product']   = $this->productFactory->create($array);
        $returnArray['design']    = $this->designFactory->create($array);
        $returnArray['ID']        = $this->getIDFromParts(
            $returnArray['article']->getID(),
            $returnArray['product']->getProductID(),
            $returnArray['design']->getID(),
            $returnArray['product']->getSize()
        );
        $returnArray['totalSold'] = $array['totalSold'];


        return parent::convertArrayToObject($returnArray);
    }

    /**
     * Gets an Item's ID from it's associated parts
     *
     * @param string $articleID
     * @param string $productID
     * @param string $designID
     * @param string $size
     *
     * @return string
     */
    public function getIDFromParts($articleID, $productID, $designID, $size)
    {
        return implode('|', [$articleID, $productID, $designID, $size]);
    }

    /**
     * Returns an array of parts from an ID
     *
     * @param string $ID
     *
     * @return string[]
     */
    public function getPartsFromID($ID)
    {
        return explode('|', $ID);
    }
}