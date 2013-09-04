<?php

/**
 * Created by: Gregory Benner.
 * Date: 9/4/13
 */

use \Entity\CartItem;
use \Entity\Coupon;

class ShoppingCart
{
    /** @var Settings */
    private $settings;
    /** @var array */
    public $sessionArray;

    public function __construct(Settings $settings)
    {
        if (session_id() == '') {
            session_start();
        }

        $this->settings = $settings;

        if (!array_key_exists('ShoppingCart', $_SESSION)) {
            // if the shopping cart array does not exist in the session, initialize it using emptyCart()
            $this->emptyCart();
        } else {
            //The cart element exists, bring it into the object
            $this->sessionArray = $_SESSION['ShoppingCart'];
        }
    }

    /**
     * Returns an array of cart items
     *
     * @return \Entity\CartItem[]
     *
     * @throws Exception
     */
    public function getCartItems()
    {
        if (array_key_exists('CartItems', $this->sessionArray)) {
            return $this->sessionArray['CartItems'];
        } else {
            throw new Exception('CartItem array does not exist');
        }
    }

    /**
     * Returns a single cart item by it's ID
     *
     * @param string $ID This is actually the Item's ID + the size (ex: 164XL or $ID . $size)
     *
     * @return CartItem;
     *
     * @throws Exception
     */
    public function getByID($ID)
    {
        if (array_key_exists($ID, $this->sessionArray['CartItems'])) {
            return $this->sessionArray['CartItems'][$ID];
        } else {
            throw new Exception('CartItem does not exist');
        }
    }

    /**
     * Deletes the given cart item
     *
     * @param CartItem $cartItem
     */
    public function deleteCartItem(CartItem $cartItem)
    {
        if (array_key_exists($cartItem->getID(), $this->sessionArray['CartItems'])) {
            unset($this->sessionArray['CartItems'][$cartItem->getID()]);
        }
    }

    /**
     * Persist a cart item to the session array
     *
     * @param CartItem $cartItem
     */
    public function persist(CartItem $cartItem)
    {
        $this->sessionArray['CartItems'][$cartItem->getID()] = $cartItem;
    }

    /**
     * Empties the shopping cart of everything. Items, coupons, and shipping stuff.
     */
    public function emptyCart()
    {
        $this->sessionArray = ['CartItems' => [], 'Coupon' => null];
    }

    /**
     * Returns the given coupon, if there is none throws an exception
     *
     * @return Coupon;
     *
     * @throws Exception
     */
    public function getCoupon()
    {
        if (array_key_exists('Coupon', $this->sessionArray)) {
            $coupon = $this->sessionArray['Coupon'];
            if (!is_null($coupon)) {
                return $coupon;
            } else {
                throw new Exception('No coupon found');
            }
        } else {
            throw new Exception('No coupon found');
        }
    }

    /**
     * Set the coupon to the given entity
     *
     * @param Coupon $coupon
     *
     * @throws Exception
     */
    public function setCoupon(Coupon $coupon)
    {
        if ($coupon->getUsesRemaining() > 0) {
            $this->sessionArray['Coupon'] = $coupon;
        } else {
            throw new Exception('Coupon does not have enough uses left.');
        }
    }

    /**
     * Delete the coupon from this shopping cart
     */
    public function deleteCoupon()
    {
        $this->sessionArray['Coupon'] = null;
        unset($this->sessionArray['Coupon']);
    }

    /**
     * Returns the exact amount that the coupon discounts from the cart
     *
     * @return float|int
     */
    public function getCouponDiscount()
    {
        try{
            $coupon = $this->getCoupon();
            if ($coupon->isPercent()) {
                $percentage = $coupon->getAmount() / 100;

                $discount = ($this->getSubtotal() * $percentage) * -1;
            } else {
                $amount = $coupon->getAmount();

                $discount = $amount * -1;
            }
        } catch(Exception $e){
            $discount = 0;
        }

        return $discount;
    }

    /**
     * Returns the SubTotal.
     * The SubTotal is the sum of the cart items and nothing else (no coupons or shipping stuff)
     *
     * @return float|int
     */
    public function getSubtotal()
    {
        $subtotal = 0;

        foreach ($this->getCartItems() as $cartItem) {
            /** @var \Entity\CartItem $cartItem */
            $itemTotal = $this->settings->getItemCurrentPrice($cartItem->item) * $cartItem->getQuantity();

            $subtotal += $itemTotal;
        }

        return $subtotal;
    }

    /**
     * Returns the full total for the cart, shipping, coupons, and items all included
     *
     * @return float|int
     */
    public function getFinalTotal()
    {
        return $this->getSubtotal() + $this->getCouponDiscount();
    }

    /**
     * Save the state of this cart to the session array on destruction
     */
    public function __destruct()
    {
        $_SESSION['ShoppingCart'] = [];

        $_SESSION['ShoppingCart'] = $this->sessionArray;
    }
}