<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/28/13
 */

$template = new FDTSmarty($config, 'Viewport.tpl', 'Viewport', $request->getFullURI());


if (!$template->isPageCached()) {
    $settings     = new Settings($database, $config);
    $itemsFactory = new \Factory\Item($database, $settings);

    /* Try to grab the shirt from the database, if it fails, forward the user to /404 */
    try{
        $item = $itemsFactory->getByName($settings->decodeName($request->get(1)));

        /* If there is no articleID set, set it to the first article's ID */
        $articleID = (!is_null($request->get(2)) ? Sanitize::cleanInteger($request->get(2)) : $item->getFirstArticle()->getID());

        $category = $settings->getItemCategory($item->getDisplayDate(), $item->getTotalSold(), $item->getSalesLimit());

        $template->assign('name', $item->getName());
        $template->assign('displayDate', $item->getDisplayDate());
        $template->assign('designImageURL', $item->getFormattedDesignImage(800, 800, 'jpg'));
        $template->assign('salesLimit', $item->getSalesLimit());
        $template->assign('votes', $item->getVotes());
        $template->assign('URLName', $item->getURLName());
        $template->assign('permalink', $item->getPermalink());

        /* Get the primary and secondary items' information */
        foreach ($item->getArticles() as $article) {
            $product = $item->getProduct($article->getProductID());
            /* Set the gender article id's */
            if ($product->getType() == 'male') {
                $template->assign('maleArticleID', $article->getID());
            } elseif ($product->getType() == 'female') {
                $template->assign('femaleArticleID', $article->getID());
            }
            /* Set the primary and secondary information */
            if ($article->getID() == $articleID) {
                //Primary
                $template->assign('description', $article->getDescription());
                $template->assign('articleImageURL', $article->getFormattedArticleImage(400, 450, 'png'));
                $template->assign('price', $settings->getItemCurrentPrice($article->getBaseRetail(), $category)->getNiceFormat());
                $template->assign('type', $product->getType());
                $template->assign('sizesAvailable', $product->getSizesAvailable());
            } else {
                //Secondary
                $template->assign('secondaryArticleImageURL', $article->getFormattedArticleImage(400, 450, 'png'));
                $template->assign('secondaryDescription', $article->getDescription());
            }
        }
        
    } catch(Exception $e){
        header('/404');
        die();
    }
}

$template->output();