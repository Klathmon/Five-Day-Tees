<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$itemID = $request->get(1);

$template = new FDTSmarty($config, 'Viewport.tpl', 'Viewport', $itemID);


if (!$template->isPageCached()) {
    $settings     = new Settings($database, $config);
    $itemsFactory = new \Factory\Item($database, $settings);

    /* Try to grab the shirt from the database, if it fails, forward the user to /404 */
    try{
        $items  = $itemsFactory->getAssociatedByID($itemID);

        foreach ($items as $item) {
            $articleImageURL = $item->article()->getFormattedArticleImage(400, 450, 'png');
            if ($item->getID() == $itemID) {
                $template->assign('name', $item->design()->getName());
                $template->assign('displayDate', $item->design()->getDisplayDate());
                $template->assign('salesLimit', $item->design()->getSalesLimit());
                $template->assign('votes', $item->design()->getVotes());
                $template->assign('ID', $item->getURLName());
                $template->assign('permalink', $item->getPermalink());
                $template->assign('description', $item->article()->getDescription());
                $template->assign('articleImageURL', $articleImageURL);
                $template->assign('price', $settings->getItemCurrentPrice($item->article()->getBaseRetail(), $item->getCategory())->getNiceFormat());
                $template->assign('type', $item->product()->getType());
                $template->assign('size', $item->product()->getSize());
            } elseif ($template->getTemplateVars('articleImageURL') != '' && $template->getTemplateVars('articleImageURL') == $articleImageURL) {
                $template->assign('secondaryArticleImageURL', $item->article()->getFormattedArticleImage(400, 450, 'png'));
                $template->assign('secondaryDescription', $item->article()->getDescription());
            }
            
            $sizesTemp[$item->product()->getSize()] = $item->getID();
            $types[$item->product()->getType()] = $item->getID();
        }
        
        //Rearrange the sizes and types arrays//
        foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'N/A'] as $size){
            $sizes[$size] = (isset($sizesTemp[$size]) ? $sizesTemp[$size] : null);
            if(!isset($sizes[$size])){
                unset($sizes[$size]);
            }
        }
        
        $template->assign('sizes', $sizes);
        $template->assign('types', $types);

    } catch(Exception $e){
        header('/404');
        die();
    }
}

$template->output();