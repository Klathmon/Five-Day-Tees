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

    public function create($articleID = null, $size = null, $quantity = null)
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

        $purchasePrice = $this->settings->getItemCurrentPrice(new Currency($results['baseRetail']), $category);
        
        if (in_array(strtolower($category), ['vault', 'queue', 'disabled'])) {
            throw new Exception('You can\' sell that shirt!');
        } elseif (stripos($results['sizesAvailable'], $size) === false) {
            throw new Exception('That size does not exist!');
        }

        $parsedArray = $this->parseResults([$results]);
        $parsedArray = reset($parsedArray);


        $bigArray                  = $parsedArray['design'];
        $bigArray['article']       = $this->articleFactory->convertArrayToObject(reset($parsedArray['articles']));
        $bigArray['product']       = $this->productFactory->convertArrayToObject(reset($parsedArray['products']));
        $bigArray['size']          = $size;
        $bigArray['quantity']      = $quantity;
        $bigArray['purchasePrice'] = $purchasePrice;

        return $this->convertArrayToObject($bigArray);
    }

}