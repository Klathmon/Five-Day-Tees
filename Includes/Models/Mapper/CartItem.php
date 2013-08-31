<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

namespace Mapper;

use Entity\Entity, \Settings;

class CartItem implements Mapper
{
    private $subtotal;

    /** @var Settings */
    private $settings;

    public function __construct(Settings $settings)
    {
        if (session_id() == '') {
            session_start();
        }

        $this->settings = $settings;
        $this->subtotal = 0;
    }

    /**
     * @param string $ID This is actually the Item's ID + the size (ex: 164XL or $ID . $size)
     *
     * @return \Entity\CartItem;
     */
    public function getByID($ID)
    {
        return (empty($_SESSION['entities'][$ID]) ? false : $_SESSION['entities'][$ID]);
    }

    public function listAll($start = 0, $stop = null)
    {
        return $_SESSION['entities'];
    }

    public function delete(Entity $entity)
    {
        unset($_SESSION['entities'][$entity->getID()]);

        $this->recalculateSubtotal();
    }

    public function persist(Entity $entity)
    {
        $_SESSION['entities'][$entity->getID()] = $entity;

        $this->recalculateSubtotal();
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function emptyCart()
    {
        $_SESSION['entities'] = array();
    }

    private function recalculateSubtotal()
    {
        $this->subtotal = 0;

        foreach ($_SESSION['entities'] as $entity) {
            /** @var \Entity\CartItem $entity */
            $this->subtotal += $this->settings->getItemCurrentPrice($entity->item) * $entity->getQuantity();
        }
    }
}