<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/4/13
 */

use Object\Coupon;
use Object\SalesItem;
use Object\ShippingMethod;

class ShoppingCart
{
    /** @var PDO */
    private $database;
    /** @var Settings */
    private $settings;
    /** @var \Factory\SalesItem */
    private $salesItemFactory;
    /** @var array */
    private $storageArray;

    public function __construct(PDO $database, Settings $settings)
    {
        /* Start a session if one is not already started */
        if (session_id() == '') {
            session_start();
        }

        $this->settings         = $settings;
        $this->salesItemFactory = new \Factory\SalesItem($database, $settings);

        if (!array_key_exists('ShoppingCart', $_SESSION)) {
            /* if the shopping cart array does not exist in the session, initialize it using emptyCart() */
            $this->emptyCart();
        } else {
            /* The cart element exists, bring it into the object */
            $this->storageArray = $_SESSION['ShoppingCart'];
        }
    }

    /**
     * Returns an array all of the SalesItems
     *
     * @return SalesItem[]
     */
    public function getAllSalesItems()
    {
        return $this->storageArray['SalesItems'];
    }

    /**
     * Returns a single SalesItem by it's key-parts
     *
     * @param $articleID
     * @param $size
     *
     * @throws Exception
     * @return SalesItem;
     */
    public function getSalesItem($articleID, $size)
    {
        $key = $this->salesItemFactory->getKey($articleID, $size);

        return $this->getSalesItemByKey($key);
    }

    /**
     * Returns a single SalesItem by it's key
     *
     * @param $key
     *
     * @return SalesItem
     * @throws Exception
     */
    public function getSalesItemByKey($key)
    {
        $salesItemArray = $this->getAllSalesItems();

        if (array_key_exists($key, $salesItemArray)) {
            return $salesItemArray[$key];
        } else {
            throw new Exception('No SalesItem with that ID exists');
        }
    }

    /**
     * @param int    $articleID
     * @param string $size
     * @param int    $quantity
     */
    public function addSalesItem($articleID, $size, $quantity)
    {
        $salesItem = $this->salesItemFactory->create($articleID, $size, $quantity);

        $this->persistSalesItem($salesItem);
    }

    /**
     * Saves the given SalesItem to the session
     *
     * @param SalesItem $salesItem
     */
    public function persistSalesItem(SalesItem $salesItem)
    {
        $this->storageArray['SalesItems'][$salesItem->getID()] = $salesItem;
    }

    /**
     * Deletes the SalesItem if it exists in the database.
     *
     * @param int    $articleID
     * @param string $size
     */
    public function deleteSalesItem($articleID, $size)
    {
        $key = $this->salesItemFactory->getKey($articleID, $size);

        if (array_key_exists($key, $this->storageArray['SalesItems'])) {
            unset($this->storageArray['SalesItems'][$key]);
        }
    }


    /**
     * Returns the coupon currently applied, if there is none it throws an exception
     *
     * @return Coupon;
     *
     * @throws Exception
     */
    public function getCoupon()
    {
        $coupon = $this->storageArray['Coupon'];
        if (!is_null($coupon)) {
            return $coupon;
        } else {
            throw new Exception('No coupon found');
        }
    }

    /**
     * Persist the given coupon to cart
     *
     * @param Coupon $coupon
     *
     * @throws Exception
     */
    public function persistCoupon(Coupon $coupon)
    {
        if ($coupon->getUsesRemaining() > 0) {
            $this->storageArray['Coupon'] = $coupon;
        } else {
            throw new Exception('Coupon does not have enough uses left.');
        }
    }

    /**
     * Sets the Coupon by it's code
     *
     * @param string $code
     */
    public function setCoupon($code)
    {
        $couponFactory = new \Factory\Coupon($this->database);

        $coupon = $couponFactory->getByCode($code);

        $this->persistCoupon($coupon);
    }

    /**
     * Delete the coupon from this shopping cart
     */
    public function deleteCoupon()
    {
        unset($this->storageArray['Coupon']);
        $this->storageArray['Coupon'] = null;
    }

    /**
     * Returns the exact amount that the coupon discounts from the cart
     *
     * @return Currency
     */
    public function getCouponDiscount()
    {
        try{
            $discount = $this->getCoupon()->getAmount();
        } catch(Exception $e){
            $discount = new Currency('0.00');
        }

        return $discount;
    }


    /**
     * Set the ShippingMethod by it's ID
     *
     * @param $ID
     *
     */
    public function setShippingMethod($ID)
    {
        $shippingMethodFactory = new \Factory\ShippingMethod($this->database);

        $shippingMethod = $shippingMethodFactory->getByID($ID);

        $this->persistShippingMethod($shippingMethod);
    }

    /**
     * Sets the shipping method to the given entity
     *
     * @param ShippingMethod $shippingMethod
     */
    public function persistShippingMethod(ShippingMethod $shippingMethod)
    {
        $this->storageArray['ShippingMethod'] = $shippingMethod;
    }

    /**
     * Returns the selected ShippingMethod
     *
     * @return \Object\ShippingMethod
     *
     * @throws Exception
     */
    public function getShippingMethod()
    {
        $shippingMethod = $this->storageArray['ShippingMethod'];
        if (!is_null($shippingMethod)) {
            return $shippingMethod;
        } else {
            throw new Exception('No Shipping Method found', 1);
        }
    }

    /**
     * Removes the current shipping method from the cart
     */
    public function deleteShippingMethod()
    {
        unset($this->storageArray['ShippingMethod']);
        $this->storageArray['ShippingMethod'] = null;
    }


    /**
     * Empties the shopping cart of everything. SalesItems, Coupons, and ShippingMethods and re-initializes everything.
     */
    public function emptyCart()
    {
        $this->storageArray = ['SalesItems' => [], 'Coupon' => null, 'ShippingMethod' => null];
    }

    /**
     * Returns the SubTotal.
     * The SubTotal is the sum of the cart items and nothing else (no coupons or shipping stuff)
     *
     * @return Currency
     */
    public function getSubtotal()
    {
        $subtotal = new Currency(0);

        foreach ($this->getAllSalesItems() as $salesItem) {
            /** @var \Object\SalesItem $salesItem */

            $itemTotalCents = $salesItem->getPurchasePrice()->getCents() * $salesItem->getQuantity();
            

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
        return Currency::createFromCents($this->getSubtotal()->getCents() + $this->getCouponDiscount()->getCents());
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

        try{
            $shipping = $this->getShippingMethod()->calculateShippingPrice($subtotal);
        } catch(Exception $e){
            if ($e->getCode() == 2) {
                throw new Exception('Total Price Too High!');
            } else {
                $shipping = new Currency(0);
            }
        }
        

        return Currency::createFromCents($subtotal->getCents() + $shipping->getCents());
    }

    /**
     * Save the state of this cart to the session array on destruction
     */
    public function __destruct()
    {
        $_SESSION['ShoppingCart'] = [];

        $_SESSION['ShoppingCart'] = $this->storageArray;
    }
}