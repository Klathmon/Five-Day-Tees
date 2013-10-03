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

    $items = $itemsFactory->getStore(true);

    foreach($items as $item){
        $itemsDisplay[] = [
            'ID' => $item->getID(),
            'name' => $item->design()->getName(),
            'description' => $item->article()->getDescription(),
            'price' => $settings->getItemCurrentPrice($item->article()->getBaseRetail(), $item->getCategory())->getNiceFormat(),
            'designImageURL' => $item->design()->getFormattedDesignImage(150, 150, 'jpg')
        ];
    }

    $layout->assign('items', $itemsDisplay);
}

$layout->output();