<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/16/13
 */

namespace Factory;

use \Currency;
use PDO;

class Order extends FactoryBase implements FactoryInterface
{
    use SQLTrait;


    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param \Object\Customer    $customer
     * @param \Object\Address     $address
     * @param string              $status
     * @param \Currency           $orderTotal
     * @param \Currency           $taxTotal
     * @param \Currency           $shippingTotal
     * @param \Currency           $itemsTotal
     * @param \Object\Coupon      $coupon
     * @param string              $paypalCorrelationID
     * @param \Object\OrderItem[] $orderItems
     *
     * @return \Object\Order
     */
    public function create(
        \Object\Customer $customer = null,
        \Object\Address $address = null,
        $status = null,
        Currency $orderTotal = null,
        Currency $taxTotal = null,
        Currency $shippingTotal = null,
        Currency $itemsTotal = null,
        \Object\Coupon $coupon = null,
        $paypalCorrelationID = null,
        $orderItems = null
    ){
        $array = [
            'customer'            => $customer,
            'address'             => $address,
            'status'              => $status,
            'orderTotal'          => $orderTotal,
            'taxTotal'            => $taxTotal,
            'shippingTotal'       => $shippingTotal,
            'itemsTotal'          => $itemsTotal,
            'coupon'              => $coupon,
            'paypalCorrelationID' => $paypalCorrelationID,
            'orderItems'          => $orderItems
        ];

        return parent::convertArrayToObject($array);
    }

    public function getAll()
    {
        $sql = $this->selectOrderInfo . $this->orderInfoFrom;
        
        $statement = $this->database->prepare($sql);
        
        $statement->execute();
        
        $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}