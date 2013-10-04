<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/4/13
 */

namespace SmartyPlugins;

class Currency
{
    static function output($params, \Smarty_Internal_Template $template)
    {
        $value = $params['amount'];

        if (get_class($value) == 'Currency') {
            /** @var \Currency $value */
            $amount = $value->getDecimal();
        } elseif (is_int($value)) {
            $amount = \Currency::createFromCents($value)->getDecimal();
        } else {
            $amount = $value;
        }

        return '$' . number_format($amount, 2);
    }
}