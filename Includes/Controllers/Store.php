<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$layout = new Layout($config, 'Store.tpl', 'Store', 'Page_Store');

if (!$layout->isPageCached()) {
    //If the page is cached, then skip all of this because it's not needed or used
    $settings    = new Settings($database, $config);
    $itemsFactory = new \Factory\Item($database, $settings);

    $items = $itemsFactory->getStore();

    foreach($items as $item){
        $category = $settings->getItemCategory($item);
        $itemsDisplay[] = [
            'URLName' => $item->getURLName(),
            'name' => $item->getDesign()->getName(),
            'description' => $item->getFirstArticle()->getDescription(),
            'price' => $settings->getItemCurrentPrice($item->getFirstArticle()->getBaseRetail(), $category)->getNiceFormat(),
            'designImageURL' => $item->getDesign()->getFormattedDesignImage(150, 150, 'jpg')
        ];
    }

    $layout->assign('items', $itemsDisplay);
}

$layout->output();