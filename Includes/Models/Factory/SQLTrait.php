<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/12/13
 */

namespace Factory;

trait SQLTrait
{
    protected $selectItemSQL = <<<SQL
    SELECT
    Design.ID AS designID, Design.name, Design.displayDate, Design.designImageURL, Design.salesLimit, Design.votes,
    Article.ID AS articleID, Article.lastUpdated, Article.description, Article.articleImageURL, Article.numberSold, Article.baseRetail,
    Product.ID AS productID, Product.cost, Product.type, Product.sizesAvailable

SQL;
    
    protected $fromDesignSQL
        = <<<SQL

  FROM Design
    LEFT JOIN Article ON (Design.ID = Article.designID)
    LEFT JOIN Product ON (Article.productID = Product.ID)

SQL;

    protected $itemCountJoinSQL
        = <<<SQL

LEFT JOIN (
    SELECT designID, SUM(numberSold) AS totalSold 
        FROM Article 
        GROUP BY designID
    ) AS itemsSold ON (Design.ID = itemsSold.designID)

SQL;

    protected $orderByDisplayDateSQL
        = <<<SQL

ORDER BY Design.displayDate DESC

SQL;





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