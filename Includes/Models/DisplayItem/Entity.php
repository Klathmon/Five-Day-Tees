<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace DisplayItem;

use Currency;

class Entity extends \Item\Entity
{
    /** @var string */
    protected $displayitemID;

    public function getEncodedName()
    {
        return urlencode($this->getArticle()->getName());
    }

    public function getPermalink()
    {
        return '/Shirt/' . $this->getEncodedName();
    }
}