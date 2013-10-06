<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace CartItem;

use Exception;

class Entity extends \Item\Entity
{
    /** @var string */
    protected $cartitemID;
    /** @var string */
    protected $size;
    /** @var int */
    protected $quantity;

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     *
     * @throws \Exception
     */
    public function setSize($size)
    {
        if (in_array($size, $this->getProduct()->getSizes())) {
            $this->size = $size;
        } else {
            throw new Exception('That size is not available');
        }

    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
}