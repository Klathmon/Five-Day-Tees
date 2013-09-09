<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/22/13
 */

/**
 * Class SpreadshirtItems
 *
 * This is the SpreadshirtItems class that deals with pulling in item information from Spreadshirt to our Database
 */
class SpreadshirtItems
{
    /** @var PDO */
    private $database;
    /** @var ConfigParser */
    private $config;
    /** @var \Factory\Design */
    private $designFactory;
    /** @var \Factory\Article */
    private $articleFactory;
    /** @var \Factory\Product */
    private $productFactory;
    /** @var Settings */
    private $settings;

    /**
     * @var array The namespaces in the XML document
     */
    private $namespaces = [];

    /**
     * @var array This is the cache of XML Documents used to speed things up
     */
    private $XMLDocumentCache = [];

    /**
     * @param PDO          $database
     * @param ConfigParser $config
     */
    public function __construct($database, $config)
    {
        $this->database       = $database;
        $this->config         = $config;
        $this->settings       = new Settings($database, $config);
        $this->designFactory  = new \Factory\Design($database);
        $this->articleFactory = new \Factory\Article($database);
        $this->productFactory = new \Factory\Product($database);
    }

    /**
     * Grabs all the shirts from Spreadshirt and parses them into my database, updating those that need it, and inserting new ones.
     *
     * @param int $amount
     * @param int $startingNumber
     */
    public function getNewItems($amount = 200, $startingNumber = null)
    {
        /* Get the information to build the URL */
        $spreadURL = $this->config->getSpreadAPIURL();
        $shopID    = $this->config->getSpreadShopID();

        $url = "{$spreadURL}shops/{$shopID}/articles?";

        /* Setup the RequestParser data */
        $query = [
            'fullData'  => 'true',
            'sortField' => 'created',
            'sortOrder' => 'desc',
            'limit'     => $amount
        ];
        if (!is_null($startingNumber)) {
            $query['offset'] = $startingNumber;
        }

        /* Fetch the Document */
        $XML = $this->getXMLDocument($url . http_build_query($query));

        /* Get the xlink namespace string from the document root */
        $this->namespaces['xlink'] = $XML->documentElement->getAttribute('xmlns:xlink');

        /* Loop through each article in the document and process each one */
        foreach ($XML->getElementsByTagName('article') as $article) {
            $this->processArticle($article);
        }
    }

    /**
     * Process through a single article.
     *
     * @param DOMElement $articleElement
     */
    private function processArticle($articleElement)
    {
        $articleID = $articleElement->getAttribute('id');

        /* Try to get this Article */
        try{
            $article = $this->articleFactory->getByID($articleID);

            /* Check if the retrieved article needs to be updated */
            $FDTLastUpdated    = $article->getLastUpdated();
            $spreadLastUpdated = DateTime::createFromFormat(
                'Y-m-d\TH:i:s\Z',
                $articleElement->getElementsByTagName('modified')->item(0)->nodeValue,
                new DateTimeZone('UTC')
            );

            /* Check if the lastUpdated time in our database is older than the lastUpdated time from Spreadshirt */
            if ($FDTLastUpdated->format('U') < $spreadLastUpdated->format('U')) {
                throw new Exception('Article Needs to be updated!');
            }
        } catch(Exception $e){
            /* This Article doesn't exist, or needs to be updated. So create it from spreadshirt! */

            /* First, get the associated product and design (creating them if necessary) */
            $product = $this->getProduct($articleElement);
            $design  = $this->getDesign($articleElement);

            $designID    = $design->getID();
            $productID   = $product->getID();
            $lastUpdated = DateTime::createFromFormat('U', time());
            $description = (isset($articleElement->getElementsByTagName('description')->item(0)->nodeValue) ? 
                $articleElement->getElementsByTagName('description')->item(0)->nodeValue : '');
            $numberSold  = 0;
            $baseRetail  = ($product->getCost()->getCents() <= $this->settings->getRetail()->getCents() ? $this->settings->getRetail()
                : $product->getCost());

            //There is probably a better way to do this, but i don't really care at this point.
            $resources = $articleElement->getElementsByTagName('resource');
            foreach ($resources as $resource) {
                /** @var DOMElement $resource */
                if ($resource->getAttribute('type') == 'product') {
                    //Remove the http: or https: from the url so it's cross compatible with https/http on my server
                    $articleImageURL = str_replace(['http:', 'https:'], '', $resource->getAttributeNS($this->namespaces['xlink'], 'href'));
                }
            }
            
            $article = $this->articleFactory->create(
                $articleID,
                $designID,
                $productID,
                $lastUpdated,
                $description,
                $articleImageURL,
                $numberSold,
                $baseRetail
            );
            
            $this->articleFactory->persist($article);
        }
    }

    /**
     * @param DOMElement $articleElement
     *
     * @return \Object\Product
     */
    private function getProduct($articleElement)
    {
        /* Fetch the Product Document and ProductID */
        $productDocument = $this->getProductDocumentFromArticle($articleElement);
        $productID       = $productDocument->documentElement->getAttribute('id');

        /* Try to get the product, if it throws an exception then create it */
        try{
            $product = $this->productFactory->getByID($productID);
        } catch(Exception $e){
            $productName = $productDocument->getElementsByTagName('name')->item(0)->nodeValue;
            $sizesNode   = $productDocument->getElementsByTagName('sizes')->item(0);

            $type = (stripos($productName, 'Women') !== false ? 'female' : 'male');
            $cost = new Currency($articleElement->getElementsByTagName('vatIncluded')->item(0)->nodeValue);

            /** @var DOMElement $sizesNode */
            foreach ($sizesNode->getElementsByTagName('size') as $sizeNode) {
                /** @var DOMElement $sizeNode */
                $sizesAvailable[] = $sizeNode->getElementsByTagName('name')->item(0)->nodeValue;
            }

            $product = $this->productFactory->create($productID, $cost, $type, $sizesAvailable);
            $this->productFactory->persist($product);
        }

        return $product;
    }

    /**
     * @param DOMElement $articleElement
     *
     * @return \Object\Design
     */
    private function getDesign($articleElement)
    {
        $name = $articleElement->getElementsByTagName('name')->item(0)->nodeValue;

        try{
            $design = $this->designFactory->getByName($name);
        } catch(Exception $e){
            $numberOfDays = $this->settings->getDaysApart();

            $displayDate = $this->settings->getLastDate()->modify("+$numberOfDays days");
            $salesLimit  = $this->settings->getSalesLimit();
            $votes       = 0;

            //There is probably a better way to do this, but i don't really care at this point.
            $resources = $articleElement->getElementsByTagName('resource');
            foreach ($resources as $resource) {
                /** @var DOMElement $resource */
                if ($resource->getAttribute('type') == 'composition') {
                    //Remove the http: or https: from the url so it's cross compatible with https/http on my server
                    $designImageURL = str_replace(['http:', 'https:'], '', $resource->getAttributeNS($this->namespaces['xlink'], 'href'));
                }
            }

            $design = $this->designFactory->create(null, $name, $displayDate, $designImageURL, $salesLimit, $votes);
            $this->designFactory->persist($design);
        }

        return $design;
    }


    /**
     * Returns the XML DOMDocument at the specified URL
     * This uses a Script-lifetime cache to prevent re-fetching the same documents more than once
     * This DRASTICALLY improves performance!
     *
     * @param string $url
     *
     * @return DOMDocument
     */
    private function getXMLDocument($url)
    {
        if (array_key_exists($url, $this->XMLDocumentCache)) {
            $DOMDocument = $this->XMLDocumentCache[$url];
        } else {
            $DOMDocument               = new DOMDocument('1.0', 'UTF-8');
            $DOMDocument->formatOutput = ($this->config->debugModeOn() ? true : false);
            $DOMDocument->load($url);

            $this->XMLDocumentCache[$url] = $DOMDocument;
        }


        return $DOMDocument;
    }

    /**
     * Returns the Product Type Document for the given Article
     *
     * @param DOMElement $article
     *
     * @return DOMDocument
     */
    private function getProductDocumentFromArticle($article)
    {
        /** @var $productElement DOMElement */
        $productElement  = $article->getElementsByTagName('productType')->item(0);
        $productURL      = $productElement->getAttributeNS($this->namespaces['xlink'], 'href');
        $productDocument = $this->getXMLDocument($productURL);

        return $productDocument;
    }
}