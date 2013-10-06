<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$layout = new Layout($config, 'FAQ.tpl', 'Frequently Asked Questions', 'Page_FAQ');

if(!$layout->isPageCached()){
    $shippingMethodFactory = new \ShippingMethod\Factory($database);
    
    $shippingMethods = $shippingMethodFactory->getAllEnabledFromDatabase();
    
    $layout->assign('shippingMethods', $shippingMethods);
}

$layout->output();