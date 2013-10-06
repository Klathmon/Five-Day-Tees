<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/19/13
 */

namespace Traits;

trait SQLStatements
{
    
    protected $SQLItemSelect = <<<SQL
SELECT
    Design.*, Article.*, Product.*, itemsSold.totalSold
  FROM Article
    LEFT JOIN Design USING (designID)
    LEFT JOIN Article_Product USING (articleID)
    LEFT JOIN Product USING (productID, size)
    LEFT JOIN (
        SELECT 
            designID, SUM(numberSold) AS totalSold
          FROM Article
          GROUP BY designID
      ) AS itemsSold USING (designID)
SQL;
    
    protected $SQLItemSelectSuffix = <<<SQL
  ORDER BY displayDate DESC
SQL;

}