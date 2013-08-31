<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

namespace Entity;

use \Settings;

class CartItem implements Entity
{
    /** @var Item */
    public $item;

    /** @var Settings */
    private $settings;

    /** @var string */
    private $size;

    /** @var string */
    private $currentPrice;

    /** @var int */
    private $quantity;

    public function __construct(Settings $settings, Item $item, $size)
    {
        $this->settings = $settings;
        $this->item     = $item;
        $this->size     = $size;
        $this->quantity = 1;

        $this->currentPrice = $this->settings->getItemCurrentPrice($this->item);
    }

    public function getID()
    {
        return $this->item->getID() . $this->size;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function addOneItem()
    {
        $this->quantity += 1;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getCurrentPrice()
    {
        return $this->currentPrice;
    }

    /**
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }
}