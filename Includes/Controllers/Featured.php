<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

$layout = new Layout($config, 'Featured.tpl', 'Featured', 'Page_Featured');

if (!$layout->isPageCached()) {
    //If the page is cached, then skip all of this because it's not needed or used
    $settings    = new Settings($database, $config);
    $itemsFactory = new \Factory\Item($database, $settings);

    $items = $itemsFactory->getFeatured();
    
    foreach($items as $item){
        $category = $settings->getItemCategory($item);
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