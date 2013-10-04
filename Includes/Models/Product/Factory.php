<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Product;

use Currency;
use DOMDocument;
use DOMElement;
use Exception;
use Settings;
use Traits\Database;

class Factory extends \Abstracts\Factory
{
    use Database;

    /** @var Settings */
    protected $settings;

    public function __construct($database, $settings)
    {
        parent::__construct($database);
        $this->settings = $settings;
    }

    /**
     * @param DOMElement  $articleElement
     * @param DOMDocument $productDocument
     * @param int         $articleID
     *
     * @return object
     */
    public function createFromSpreadshirt($articleElement, $productDocument, $articleID)
    {
        $productID = $articleElement->getAttribute('id');

        try{
            $product = $this->getByIDFromDatabase($productID);
        } catch(Exception $e){
            //There is probably a better way to do this, but i don't really care at this point.
            $resources = $articleElement->getElementsByTagName('resource');
            foreach ($resources as $resource) {
                /** @var DOMElement $resource */
                if ($resource->getAttribute('type') == 'product') {
                    //Remove the http: or https: from the url so it's cross compatible with https/http on my server
                    $productImageURL = str_replace(
                        ['http:', 'https:'], '', $resource->getAttributeNS('http://www.w3.org/1999/xlink', 'href')
                    );
                }
            }

            /** @var DOMElement $sizesNode */
            $sizesNode = $productDocument->getElementsByTagName('sizes')->item(0);
            foreach ($sizesNode->getElementsByTagName('size') as $sizeNode) {
                /** @var DOMElement $sizeNode */
                $sizes[] = $sizeNode->getElementsByTagName('name')->item(0)->nodeValue;
            }

            $array['productID']       = $productID;
            $array['articleID']       = $articleID;
            $array['description']     = (isset($articleElement->getElementsByTagName('description')->item(0)->nodeValue) ? $articleElement->getElementsByTagName('description')->item(0)->nodeValue : '');
            $array['productImageURL'] = $productImageURL;
            $array['cost']            = $articleElement->getElementsByTagName('vatIncluded')->item(0)->nodeValue;
            $array['retail']          = (Currency::createFromDecimal($array['cost'])->getCents() <= $this->settings->getRetail()->getCents() ? $this->settings->getRetail()->getDecimal() : $array['cost']);
            $array['sold']            = 0;
            $array['type']            = (stripos($productDocument->getElementsByTagName('name')->item(0)->nodeValue, 'Women') !== false ? 'female' : 'male');
            $array['sizes']           = implode(',', $sizes);

            $product = $this->convertArrayToObject($array);
        }

        return $product;
    }

    protected function convertObjectToArray($entity)
    {
        $array = parent::convertObjectToArray($entity);

        /** @var $array Currency[] */
        $array['cost']   = $array['cost']->getDecimal();
        $array['retail'] = $array['retail']->getDecimal();
        /** @var $array string[] */
        $array['sizes'] = implode(',', $array['sizes']);

        return $array;
    }

    protected function convertArrayToObject($array)
    {
        $array['cost']   = Currency::createFromDecimal($array['cost']);
        $array['retail'] = Currency::createFromDecimal($array['retail']);
        $array['sizes']  = explode(',', $array['sizes']);

        return parent::convertArrayToObject($array);
    }
}