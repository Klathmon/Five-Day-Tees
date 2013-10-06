<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/4/13
 */

$category = $request->get(0);

$layout = new Layout($config, 'Page.tpl', $category, 'Page_' . $category);

if (!$layout->isPageCached()) {
    //If the page is cached, then skip all of this because it's not needed or used
    $settings    = new Settings($database, $config);
    $itemFactory = new \DisplayItem\Factory($database, $settings);
    
    switch($category){
        case '':
        case 'Featured':
            $layout->assign('subHeader', 'Featured Shirts:');
            $items = $itemFactory->getFeaturedFromDatabase();
            break;
        case 'Store':
            $layout->assign('subHeader', 'Store Shirts:');
            $items = $itemFactory->getStoreFromDatabase();
            break;
        case 'Vault':
            $layout->assign('subHeader', 'Vault Shirts:');
            $items = $itemFactory->getVaultFromDatabase();
            break;
    }

    $layout->assign('ID', $category);
    $layout->assign('items', $items);
}

$layout->output();