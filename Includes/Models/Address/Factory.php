<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/6/13
 */

namespace Address;

use Exception;
use Traits\Database;

class Factory extends \Abstracts\Factory
{
    use Database;
    
    public function createFromPayPal($paypalResponse)
    {
        $array['address1'] = $paypalResponse['PAYMENTREQUEST_0_SHIPTOSTREET'];
        $array['address2'] = null;
        $array['city']     = $paypalResponse['PAYMENTREQUEST_0_SHIPTOCITY'];
        $array['state']    = $paypalResponse['PAYMENTREQUEST_0_SHIPTOSTATE'];
        $array['zip']      = $paypalResponse['PAYMENTREQUEST_0_SHIPTOZIP'];
        $array['country']  = $paypalResponse['PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME'];

        try{
            $address = $this->getByAddressFromDatabase($array);
        }catch(Exception $e){
            $address = $this->createFromData($array);
        }
        
        return $address;
    }
    
    public function getByAddressFromDatabase($array)
    {
        $values = '';
        foreach ($array as $name => $value) {
            $values .= $name . '=:' . $name;

            end($array);
            if ($name !== key($array)) {
                $values .= ' AND ';
            }
        }

        $statement = $this->database->prepare("SELECT * FROM Address WHERE $values LIMIT 1");

        foreach ($array as $name => $value) {
            $statement->bindValue(':' . $name, $value);
        }
        
        return $this->executeAndParse($statement)[0];
    }
}