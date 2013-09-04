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
    /** @var \Mapper\Item */
    private $itemMapper;
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
        $this->database   = $database;
        $this->config     = $config;
        $this->itemMapper = new \Mapper\Item($database, $config);
        $this->settings   = new Settings($database, $config);
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
     * @param DOMElement $article
     */
    private function processArticle($article)
    {
        /* Fetch the ProductType document to be able to find the gender of the item */
        $productTypeDocument = $this->getProductDocumentFromArticle($article);
        $productName         = $productTypeDocument->getElementsByTagName('name')->item(0)->nodeValue;

        /* Find the Name and Gender */
        $name   = $article->getElementsByTagName('name')->item(0)->nodeValue;
        $gender = (stripos($productName, 'Women') !== false ? 'female' : 'male');

        /* Fetch this shirt by it's name and gender */
        $item = $this->itemMapper->getItemByNameGender($name, $gender);

        if ($item === false) {
            /* This item is not in our database. Create, fill, and persist the item */
            $item = new \Entity\Item();

            $this->fillItem($article, $item);

            $this->itemMapper->persist($item);
        } else {
            /* This item is in our database, check if it needs to be updated */
            $itemLastUpdated   = $item->getLastUpdated();
            $spreadLastUpdated = DateTime::createFromFormat(
                'Y-m-d\TH:i:s\Z',
                $article->getElementsByTagName('modified')->item(0)->nodeValue,
                new DateTimeZone('UTC')
            );

            /* Check if the lastUpdated time in our database is older than the lastUpdated time from Spreadshirt */
            if ($itemLastUpdated->format('U') < $spreadLastUpdated->format('U')) {
                /*The Item was modified on Spreadshirt, so update the current item and persist it */
                $this->fillItem($article, $item);

                $this->itemMapper->persist($item);
            }
        }
    }

    /**
     * Fills the given $item with all the data from the given $article
     *
     * @param DOMElement   $article
     * @param \Entity\Item $item
     */
    private function fillItem($article, $item)
    {
        /** @var DOMElement $defaultDesignNode */

        $productTypeDocument = $this->getProductDocumentFromArticle($article);
        $defaultDesignNode   = $article->getElementsByTagName('defaultDesign')->item(0);
        $descriptionNode     = $article->getElementsByTagName('description')->item(0);

        /* Fetch the data from the XML */
        $name        = $article->getElementsByTagName('name')->item(0)->nodeValue;
        $gender      = (stripos($productTypeDocument->getElementsByTagName('name')->item(0)->nodeValue, 'Women') !== false ? 'female' : 'male');
        $articleID   = $article->getAttribute('id');
        $designID    = $defaultDesignNode->getAttribute('id');
        $description = (isset($descriptionNode->nodeValue) ? $descriptionNode->nodeValue : '');
        $cost        = $article->getElementsByTagName('vatIncluded')->item(0)->nodeValue;
        $retail      = ($cost <= $this->settings->getRetail() ? $this->settings->getRetail() : $cost);
        $resources   = $article->getElementsByTagName('resource');
        $sizesNode   = $productTypeDocument->getElementsByTagName('sizes')->item(0);
        $lastUpdated = DateTime::createFromFormat('U', time());

        foreach ($resources as $resource) {
            /** @var DOMElement $resource */
            if ($resource->getAttribute('type') == 'product') {
                $productImage = $resource->getAttributeNS($this->namespaces['xlink'], 'href');
            } elseif ($resource->getAttribute('type') == 'composition') {
                $designImage = $resource->getAttributeNS($this->namespaces['xlink'], 'href');
            }
        }

        /** @var DOMElement $sizesNode */
        foreach ($sizesNode->getElementsByTagName('size') as $sizeNode) {
            /** @var DOMElement $sizeNode */
            $sizesAvailable[] = $sizeNode->getElementsByTagName('name')->item(0)->nodeValue;
        }

        /* Insert the data into the shirt item */
        $item->setName($name);
        $item->setGender($gender);
        $item->setArticleID($articleID);
        $item->setDesignID($designID);
        $item->setDescription($description);
        $item->setCost($cost);
        $item->setRetail($retail);
        $item->setProductImage($productImage);
        $item->setDesignImage($designImage);
        $item->setSizesAvailable($sizesAvailable);
        $item->setLastUpdated($lastUpdated);

        /* Set the Common data for the shirt */
        $this->setCommonData($item);
    }

    /**
     * This sets the Common data for an item.
     * If the item is new, it puts in defaults, otherwise it will use the information already in the database.
     *
     * @param \Entity\Item $item
     */
    private function setCommonData($item)
    {
        list($salesLimit, $displayDate, $votes) = $this->itemMapper->getItemsCommonByName($item->getName());

        if ($salesLimit === false || $displayDate === false || $votes === false) {
            /* That name does not exist, use defaults for the data */
            $numberOfDays = $this->settings->getDaysApart();

            $displayDate = $this->itemMapper->getLastDate()->modify("+$numberOfDays days");
            $salesLimit  = $this->settings->getSalesLimit();
            $votes       = 0;
        }

        /* Set the data to the item */
        $item->setSalesLimit($salesLimit);
        $item->setDisplayDate($displayDate);
        $item->setVotes($votes);
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
        /** @var $productTypeElement DOMElement */
        $productTypeElement  = $article->getElementsByTagName('productType')->item(0);
        $productTypeURL      = $productTypeElement->getAttributeNS($this->namespaces['xlink'], 'href');
        $productTypeDocument = $this->getXMLDocument($productTypeURL);

        return $productTypeDocument;
    }
}