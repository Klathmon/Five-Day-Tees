<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/6/13
 */

namespace OrderItem;

use Exception;
use PDO;
use Traits\Database;

class Factory extends \CartItem\Factory
{
    use Database;

    public function createFromPayPal($paypalResponse, $orderID)
    {
        $prefix = 'L_PAYMENTREQUEST_0_';

        for ($x = 0; array_key_exists($prefix . 'NUMBER' . $x, $paypalResponse); $x++) {
            if (substr($paypalResponse[$prefix . 'NAME' . $x], 0, 7) != 'COUPON:') {
                $ID = $paypalResponse[$prefix . 'NUMBER' . $x];

                $passThru['orderID']       = $orderID;
                $passThru['purchasePrice'] = $paypalResponse[$prefix . 'AMT' . $x];
                $passThru['quantity']      = $paypalResponse[$prefix . 'QTY' . $x];

                /** @var Entity $orderItem */
                $orderItem = parent::getByIDFromDatabase($ID, $passThru);

                $orderItems[$orderItem->getID()] = $orderItem;
            }
        }

        return $orderItems;
    }

    protected function convertArrayToObject($array, $passThru = null)
    {
        $passThru['orderitemID'] = $this->getIDFromParts(
            [
                'articleID' => $array['articleID'],
                'productID' => $array['productID'],
                'size'      => $passThru['size'],
                'orderID'   => $passThru['orderID']
            ]
        );

        return parent::convertArrayToObject($array, $passThru);
    }
}