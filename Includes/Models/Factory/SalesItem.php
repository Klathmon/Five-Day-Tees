<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/12/13
 */

namespace Factory;

use Currency;
use DateTime;
use Exception;
use PDO;
use Settings;

class SalesItem extends Design implements FactoryInterface
{
    use SQLTrait;

    /** @var Settings */
    private $settings;
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
     * @param int      $articleID
     * @param string   $size
     * @param int      $quantity
     * @param Currency $purchasePrice **Actually optional**
     *
     * @throws \Exception
     * @return \Object\SalesItem
     */
    public function create($articleID = null, $size = null, $quantity = null, $purchasePrice = null)
    {

        $sql       = $this->selectItemSQL
            . ', itemsSold.totalSold '
            . $this->fromDesignSQL
            . $this->itemCountJoinSQL
            . 'WHERE Article.ID = :ID LIMIT 1';
        $statement = $this->database->prepare($sql);

        $statement->bindValue(':ID', $articleID, PDO::PARAM_INT);

        $statement->execute();

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        if ($results == false) {
            throw new Exception('There is no Article with that ID!');
        }

        $category = $this->settings->getItemCategory(
            DateTime::createFromFormat('Y-m-d', $results['displayDate']),
            $results['totalSold'],
            $results['salesLimit']
        );

        if (is_null($purchasePrice)) {
            $purchasePrice = $this->settings->getItemCurrentPrice(new Currency($results['baseRetail']), $category);
        }

        if (in_array(strtolower($category), ['vault', 'queue', 'disabled'])) {
            throw new Exception('You can\' sell that shirt!');
        } elseif (stripos($results['sizesAvailable'], $size) === false) {
            throw new Exception('That size does not exist!');
        }

        $parsedArray = $this->parseResults([$results]);
        $parsedArray = reset($parsedArray);


        $bigArray                  = $parsedArray['design'];
        $bigArray['key']           = $this->getKey($results['articleID'], $size);
        $bigArray['article']       = $this->articleFactory->convertArrayToObject(reset($parsedArray['articles']));
        $bigArray['product']       = $this->productFactory->convertArrayToObject(reset($parsedArray['products']));
        $bigArray['size']          = $size;
        $bigArray['quantity']      = $quantity;
        $bigArray['purchasePrice'] = $purchasePrice;
        $bigArray['totalSold']     = $results['totalSold'];
        $bigArray['category']      = $category;

        return $this->convertArrayToObject($bigArray);
    }

    public function getKey($articleID, $size)
    {
        return implode('|', [$articleID, $size]);
    }

}