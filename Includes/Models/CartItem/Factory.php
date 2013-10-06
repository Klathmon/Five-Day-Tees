<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/6/13
 */

namespace CartItem;

class Factory extends \Item\Factory
{

    /**
     * @param string $ID
     * @param string $size
     *
     * @return Entity
     */
    public function getByIDAndSizeFromDatabase($ID, $size)
    {
        /** @var Entity $cartItem */
        $cartItem = $this->getByIDFromDatabase($ID);
        
        $cartItem->setSize($size);
        
        return $cartItem;
    }
}