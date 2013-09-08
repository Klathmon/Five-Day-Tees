<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

 namespace Object;
 
class ObjectBase implements ObjectInterface
 {

    public function __construct($array)
    {
        foreach($array as $key => $value){
            $this->$key = $value;
        }
    }
}