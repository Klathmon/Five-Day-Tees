<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';


$itemMapper = new \Mapper\Item($database, $config);

$item = $itemMapper->getByName('First Shirt');

$itemMapper->persist($item);

var_dump($item);

/*
$statement = $database->prepare(
    <<<SQL
    SELECT TestArticles.*, TestDesigns.*, TestProducts.*
  FROM TestArticles
    LEFT JOIN TestDesigns ON (TestArticles.DesignID = TestDesigns.ID)
    LEFT JOIN TestProducts ON (TestArticles.ProductID = TestProducts.ID)
SQL
);

$statement->execute();

$array = $statement->fetchAll(PDO::FETCH_ASSOC);

$designs = [];

foreach ($array as $row) {
    $designs[$row['DesignID']]['Name']                                          = $row['Name'];
    $designs[$row['DesignID']]['DisplayDate']                                   = DateTime::createFromFormat('Y-m-d', $row['DisplayDate']);
    $designs[$row['DesignID']]['SalesLimit']                                    = $row['SalesLimit'];
    $designs[$row['DesignID']]['DesignImageURL']                                = $row['DesignImageURL'];
    $designs[$row['DesignID']]['Votes']                                         = $row['Votes'];
    $designs[$row['DesignID']]['Articles'][$row['ID']]['ProductID']             = $row['ProductID'];
    $designs[$row['DesignID']]['Articles'][$row['ID']]['LastUpdated']           = DateTime::createFromFormat('Y-m-d H:i:s', $row['LastUpdated']);
    $designs[$row['DesignID']]['Articles'][$row['ID']]['Description']           = $row['Description'];
    $designs[$row['DesignID']]['Articles'][$row['ID']]['ArticleImageURL']       = $row['ArticleImageURL'];
    $designs[$row['DesignID']]['Articles'][$row['ID']]['NumberSold']            = $row['NumberSold'];
    $designs[$row['DesignID']]['Products'][$row['ProductID']]['Cost']           = $row['Cost'];
    $designs[$row['DesignID']]['Products'][$row['ProductID']]['Type']           = $row['Type'];
    $designs[$row['DesignID']]['Products'][$row['ProductID']]['SizesAvailable'] = $row['SizesAvailable'];
}

\Debug::dump($array, $designs);
*/