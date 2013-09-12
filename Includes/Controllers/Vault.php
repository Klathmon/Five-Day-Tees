<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */


$layout = new Layout($config, 'Vault.tpl', 'Vault', 'Page_Vault');

if (!$layout->isPageCached()) {
    //If the page is cached, then skip all of this because it's not needed or used
    $settings    = new Settings($database, $config);
    $itemsFactory = new \Factory\Item($database, $settings);

    $items = $itemsFactory->getVault();

    foreach($items as $item){
        $category = $settings->getItemCategory($item->getDisplayDate(), $item->getTotalSold(), $item->getSalesLimit());
        $itemsDisplay[] = [
            'URLName' => $item->getURLName(),
            'name' => $item->getName(),
            'description' => $item->getFirstArticle()->getDescription(),
            'price' => $settings->getItemCurrentPrice($item->getFirstArticle()->getBaseRetail(), $category)->getNiceFormat(),
            'designImageURL' => $item->getFormattedDesignImage(150, 150, 'jpg')
        ];
    }

    $layout->assign('items', $itemsDisplay);
}

$layout->output();