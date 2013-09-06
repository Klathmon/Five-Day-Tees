<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$layout = new Layout($config, 'Store.tpl', 'Store', 'Page_Store');

if (!$layout->isPageCached()) {
    //If the page is cached, then skip all of this because it's not needed or used
    $settings    = new Settings($database, $config);
    $itemsMapper = new \Mapper\Item($database, $config);


    $layout->assign('settings', $settings);
    $layout->assign('items', $itemsMapper->getStore(true));
}

$layout->output();