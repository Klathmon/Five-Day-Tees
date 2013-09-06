<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */


$layout = new Layout($config, 'Vault.tpl', 'Vault', 'Page_Vault');

if (!$layout->isPageCached()) {
    //If the page is cached, then skip all of this because it's not needed or used
    $settings    = new Settings($database, $config);
    $itemsMapper = new \Mapper\Item($database, $config);

    $layout->assign('settings', $settings);
    $layout->assign('items', $itemsMapper->getVault(true));
}

$layout->output();