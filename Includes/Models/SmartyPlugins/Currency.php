<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/4/13
 */

namespace SmartyPlugins;

use Exception;

class Currency
{
    static function output($params, \Smarty_Internal_Template $template)
    {
        /** @var \Currency $value */
        $value = $params['amount'];
        /** @var \Currency $add */
        $add = (isset($params['add']) ? $params['add'] : null);

        if (get_class($value) == 'Currency') {
            $amount = $value->getDecimal();
        } else{
            throw new Exception('Error, incorrect object passed to currency tag!');
        }
        
        if(!is_null($add)){
            $amount += $add->getDecimal();
        }

        return '$' . number_format($amount, 2);
    }
}