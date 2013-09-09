<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/31/13
 */

namespace Mapper;

use Entity\Entity, \Settings, \PDO;

class CartItem implements Mapper
{
    /** @var Settings */
    private $settings;

    private $couponManager;

    /** @var \Entity\Coupon */
    private $coupon = null;

    public function __construct(Settings $settings, PDO $database)
    {
        if (session_id() == '') {
            session_start();
        }

        $this->couponManager = new Coupon($database);
        $this->settings      = $settings;
        $this->subtotal      = 0;

        if (!array_key_exists('entities', $_SESSION)) {
            $_SESSION['entities'] = [];
        }

        if (array_key_exists('coupon', $_SESSION)) {
            $this->coupon = $_SESSION['coupon'];
        }
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
    }

    public function persist(Entity $entity)
    {
        $_SESSION['entities'][$entity->getID()] = $entity;
    }

    public function getSubtotal()
    {
        $subtotal = $this->getSubtotalNoCoupon();

        $subtotal += $this->getCouponDiscount();

        return $subtotal;
    }

    public function emptyCart()
    {
        $_SESSION['entities'] = array();
        unset($_SESSION['coupon']);
    }

    public function addCoupon($couponCode)
    {
        $coupon = $this->couponManager->getByCode($couponCode);


        if ($coupon->getUsesRemaining() > 0) {
            $this->coupon       = $coupon;
            $_SESSION['coupon'] = $coupon;
        }
    }

    /**
     * @return \Entity\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    public function deleteCoupon()
    {
        $this->coupon = null;
        unset($_SESSION['coupon']);
    }

    public function getCouponDiscount()
    {
        $subtotal = $this->getSubtotalNoCoupon();

        if (!is_null($this->coupon)) {
            $coupon = $this->coupon;
            if ($coupon->isPercent()) {
                $amount = $coupon->getAmount() / 100;

                $discount = ($subtotal * $amount) * -1;
            } else {
                $amount = $coupon->getAmount();

                $discount = $amount * -1;
            }
        } else {
            $discount = 0;
        }

        return $discount;
    }

    private function getSubtotalNoCoupon()
    {
        $subtotal = 0;

        foreach ((array)$_SESSION['entities'] as $entity) {
            /** @var \Entity\CartItem $entity */
            $subtotal += $this->settings->getItemCurrentPrice($entity->item) * $entity->getQuantity();
        }

        return $subtotal;
    }
}