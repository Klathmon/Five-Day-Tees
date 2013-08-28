<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

$settings    = new Settings($database, $config);
$itemsMapper = new \Mapper\Item($database, $config);

$layout = new Layout($config, 'Featured.tpl', 'Featured');

$layout->assign('settings', $settings);
$layout->assign('items', $itemsMapper->getFeatured(true));

$layout->output();