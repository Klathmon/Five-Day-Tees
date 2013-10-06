<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

/* Parse the request */
$amountToFetch  = (!is_null($request->get(1)) ? $request->get(1) : 250);
$startingNumber = $request->get(2);

/* Create an empty cache array */
$xmlCache = [];

/* Create the necessary objects */
$settings       = new Settings($database, $config);
$articleFactory = new \Article\Factory($database, $settings);
$productFactory = new \Product\Factory($database, $settings);

/* Get configuration settings */
$spreadURL = $config->get('SPREADSHIRT', 'API_URL');
$shopID    = $config->get('SPREADSHIRT', 'SHOP_ID');

/* Setup the query data */
$query = [
    'fullData'  => 'true',
    'sortField' => 'created',
    'sortOrder' => 'desc',
    'limit'     => $amountToFetch
];
if (!is_null($startingNumber)) {
    $query['offset'] = $startingNumber;
}

/* Build the query */
$url = "{$spreadURL}shops/{$shopID}/articles?" . http_build_query($query);

/* Fetch the Document */
$DOMDocument               = new DOMDocument('1.0', 'UTF-8');
$DOMDocument->formatOutput = $config->get('DEBUG', 'DEBUGGING');
$DOMDocument->load($url);

/* Loop through each article in the document and process each one */
foreach ($DOMDocument->getElementsByTagName('article') as $articleElement) {
    $article = $articleFactory->createFromSpreadshirt($articleElement);

    $articleID = $articleFactory->persistToDatabase($article);
    
    $articleID = ($articleID == 0 ? $article->getID() : $articleID);

    /* Get the product document */
    /** @var DOMElement $productElement */
    /** @var DOMElement $articleElement */
    $productElement = $articleElement->getElementsByTagName('productType')->item(0);
    $productURL     = $productElement->getAttributeNS('http://www.w3.org/1999/xlink', 'href');

    if (isset($xmlCache[$productURL])) {
        $productDocument = $xmlCache[$productURL];
    } else {
        $productDocument               = new DOMDocument('1.0', 'UTF-8');
        $productDocument->formatOutput = $config->get('DEBUG', 'DEBUGGING');
        $productDocument->load($productURL);
    }

    $product = $productFactory->createFromSpreadshirt($articleElement, $productDocument, $articleID);
    
    //Debug::dump($product);
    
    $productFactory->persistToDatabase($product);
}


/* All done! */