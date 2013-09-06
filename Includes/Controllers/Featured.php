<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

$layout = new Layout($config, 'Featured.tpl', 'Featured', 'Page_Featured');

if (!$layout->isPageCached()) {
    //If the page is cached, then skip all of this because it's not needed or used
    $settings    = new Settings($database, $config);
    $itemsMapper = new \Mapper\Item($database, $config);

    $layout->assign('settings', $settings);
    $layout->assign('items', $itemsMapper->getFeatured(true));
}

$layout->output();