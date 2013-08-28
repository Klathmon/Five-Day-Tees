<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */


$settings    = new Settings($database, $config);
$itemsMapper = new \Mapper\Item($database, $config);

$layout = new Layout($config, 'Vault.tpl', 'Featured');

$layout->assign('settings', $settings);
$layout->assign('items', $itemsMapper->getVault(true));

$layout->output();