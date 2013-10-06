<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Article;

use DateTime;
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
     * @param DOMElement $articleElement
     *
     * @return \Article\Entity
     */
    public function createFromSpreadshirt($articleElement)
    {
        $name = $articleElement->getElementsByTagName('name')->item(0)->nodeValue;
        try{
            $article = $this->getByNameFromDatabase($name);
        } catch(Exception $e){
            $numberOfDays = $this->settings->getDaysApart();

            //There is probably a better way to do this, but i don't really care at this point.
            $resources = $articleElement->getElementsByTagName('resource');
            foreach ($resources as $resource) {
                /** @var DOMElement $resource */
                if ($resource->getAttribute('type') == 'composition') {
                    //Remove the http: or https: from the url so it's cross compatible with https/http on my server
                    $articleImageURL = str_replace(
                        ['http:', 'https:'], '', $resource->getAttributeNS('http://www.w3.org/1999/xlink', 'href')
                    );
                }
            }

            $array['name']            = $name;
            $array['date']            = $this->settings->getLastDate()->modify("+$numberOfDays days")->format('Y-m-d');
            $array['articleImageURL'] = $articleImageURL;
            $array['salesLimit']      = $this->settings->getSalesLimit();
            $array['votes']           = 0;

            $article = $this->convertArrayToObject($array);
        }

        return $article;
    }

    public function getByNameFromDatabase($name)
    {
        $statement = $this->database->prepare('SELECT * FROM ' . $this->namespace . ' WHERE name=:name LIMIT 1');

        $statement->bindValue(':name', $name);

        return $this->executeAndParse($statement)[0];
    }

    protected function convertObjectToArray($entity)
    {
        $array = parent::convertObjectToArray($entity);

        /** @var DateTime[] $array */
        $array['date'] = $array['date']->format('Y-m-d');

        return $array;
    }

    protected function convertArrayToObject($array)
    {
        $array['date'] = DateTime::createFromFormat('Y-m-d', $array['date']);

        return parent::convertArrayToObject($array);
    }
}