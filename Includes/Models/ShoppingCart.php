<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/4/13
 */

class ShoppingCart
{
    /** @var \CartItem\Entity[] */
    private $cartItems;
    /** @var \Coupon\Entity */
    private $coupon;
    /** @var \ShippingMethod\Entity */
    private $shippingMethod;

    /** @var Settings */
    protected $settings;
    /** @var \CartItem\Factory */
    protected $cartItemFactory;

    public function __construct(PDO $database, Settings $settings)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        foreach (['cartItems', 'coupon', 'shippingMethod'] as $name) {
            $this->$name = (isset($_SESSION[get_class($this)][$name]) ? $_SESSION[get_class($this)][$name] : null);
        }

        $this->settings        = $settings;
        $this->cartItemFactory = new \CartItem\Factory($database, $settings);
    }

    public function __destruct()
    {
        foreach (['cartItems', 'coupon', 'shippingMethod'] as $name) {
            $_SESSION[get_class($this)][$name] = $this->$name;
        }
    }

    public function emptyCart()
    {
        foreach (['cartItems', 'coupon', 'shippingMethod'] as $name) {
            $this->$name = null;
            $_SESSION[get_class($this)][$name] = null;
        }
    }


    public function getCartItems()
    {
        return $this->cartItems;
    }

    public function getCartItemByID($ID)
    {
        if (isset($this->cartItems[$ID])) {
            return $this->cartItems[$ID];
        } else {
            throw new Exception('No CartItem with that ID in the Cart');
        }
    }

    public function addCartItemByItemIDAndSize($itemID, $size)
    {
        $IDParts         = $this->cartItemFactory->getPartsFromID($itemID);
        $IDParts['size'] = $size;
        $ID              = $this->cartItemFactory->getIDFromParts($IDParts);

        try{
            $cartItem = $this->getCartItemByID($ID);
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } catch(Exception $e){
            $cartItem = $this->cartItemFactory->getByIDFromDatabase($ID);
        }

        /* Throw an exception it if it's a vault, queue, or disabled */
        if (in_array($cartItem->getCategory(), ['vault', 'queue', 'disabled'])) {
            throw new Exception('That item cannot be sold');
        } else {
            $this->persistCartItem($cartItem);
        }
    }

    public function persistCartItem(\CartItem\Entity $cartItem)
    {
        if ($cartItem->getQuantity() <= 0) {
            $this->deleteCartItem($cartItem->getID());
        } else {
            $this->cartItems[$cartItem->getID()] = $cartItem;
        }
    }

    public function deleteCartItem($ID)
    {
        if (array_key_exists($ID, $this->cartItems)) {
            unset($this->cartItems[$ID]);
        }
    }


    public function getCoupon()
    {
        return $this->coupon;
    }

    public function setCoupon(\Coupon\Entity $coupon)
    {
        if($coupon->getUsesRemaining() <= 0){
            throw new Exception('That coupon cannot be used any more');
        }else{
            $this->coupon = $coupon;
        }
    }

    public function deleteCoupon()
    {
        $this->coupon = null;
    }


    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
    }

    public function deleteShippingMethod()
    {
        $this->shippingMethod = null;
    }


    public function getCouponAmount()
    {
        if (!is_null($this->coupon)) {
            return $this->coupon->getAmount();
        } else {
            return Currency::createFromCents(0);
        }
    }

    /**
     * Returns the SubTotal.
     * The SubTotal is the sum of the cart items and nothing else (no coupons or shipping stuff)
     *
     * @return Currency
     */
    public function getSubtotal()
    {
        $subtotal = Currency::createFromCents(0);

        foreach ((array)$this->cartItems as $cartItem) {
            /** @var \CartItem\Entity $cartItem */
            $itemTotalCents = $cartItem->getCurrentPrice()->getCents() * $cartItem->getQuantity();

            $subtotal = Currency::createFromCents($subtotal->getCents() + $itemTotalCents);
        }

        return $subtotal;
    }

    /**
     * Returns the SubTotal (before shipping is calculated)
     * This includes the Subtotal, and the coupon discount
     *
     * @return Currency
     */
    public function getPreShippingTotal()
    {
        return Currency::createFromCents($this->getSubtotal()->getCents() + $this->getCouponAmount()->getCents());
    }

    /**
     * Returns the full total for the cart, shipping, coupons, and items all included
     *
     * @throws Exception
     * @return Currency
     */
    public function getFinalTotal()
    {
        $subtotal = $this->getPreShippingTotal();

        if (!is_null($this->shippingMethod)) {
            $shippingCurrency = $this->shippingMethod->calculateShippingPrice($subtotal);
        } else {
            $shippingCurrency = Currency::createFromCents(0);
        }

        return Currency::createFromCents($subtotal->getCents() + $shippingCurrency->getCents());
    }
}