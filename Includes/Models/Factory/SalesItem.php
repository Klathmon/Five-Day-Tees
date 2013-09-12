<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/12/13
 */

namespace Factory;

use Currency;
use PDO;
use Settings;

class SalesItem extends Design implements FactoryInterface
{
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

    protected function convertObjectToArray($object)
    {
        $array = parent::convertObjectToArray($object);

        /** @var Currency $purchasePrice */
        $purchasePrice = $array['purchasePrice'];

        $array['article'] = $this->articleFactory->convertObjectToArray($array['article']);
        $array['product'] = $this->productFactory->convertObjectToArray($array['product']);
        $array['purchasePrice'] = $purchasePrice->getDecimal();
        
        return $array;
    }

    protected function convertArrayToObject($array)
    {
        $array['purchasePrice'] = new Currency($array['purchasePrice']);
        $array['article'] = $this->articleFactory->convertArrayToObject($array['article']);
        $array['product'] = $this->productFactory->convertArrayToObject($array['product']);
        
        return parent::convertArrayToObject($array);
    }

}