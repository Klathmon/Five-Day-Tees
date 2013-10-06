<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/6/13
 */

namespace Customer;

use Exception;
use PDO;
use Traits\Database;

class Factory extends \Abstracts\Factory
{
    use Database;

    public function createFromPayPal($paypalResponse)
    {
        $array['paypalPayerID']  = $paypalResponse['PAYERID'];
        $array['firstName']      = $paypalResponse['FIRSTNAME'];
        $array['lastName']       = $paypalResponse['LASTNAME'];
        $array['email']          = $paypalResponse['EMAIL'];
        $array['allowMarketing'] = true;

        try{
            $customer = $this->getByPaypalPayerIDFromDatabase($array['paypalPayerID']);
        } catch(Exception $e){
            $customer = $this->createFromData($array);
        }

        return $customer;
    }

    public function getByPaypalPayerIDFromDatabase($paypalPayerID)
    {
        $statement = $this->database->prepare("SELECT * FROM Customer WHERE paypalPayerID=:paypalPayerID LIMIT 1");

        $statement->bindValue(':paypalPayerID', $paypalPayerID, PDO::PARAM_STR);

        return $this->executeAndParse($statement)[0];
    }
}