<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

namespace Entity;


class CartItem implements Entity
{
    /** @var Item */
    public $item;

    /** @var string */
    private $size;

    /** @var string */
    private $currentPrice;

    /** @var int */
    private $quantity;

    public function __construct(Item $item, $size, $currentPricePerItem)
    {
        $this->item     = $item;
        $this->size     = $size;
        $this->quantity = 1;

        $this->currentPrice = $currentPricePerItem;
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