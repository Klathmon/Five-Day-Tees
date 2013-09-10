<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Object;

class Item implements ObjectInterface
{
    /** @var Design */
    protected $design;
    /** @var Article[] */
    protected $articles;
    /** @var Product[] */
    protected $products;

    public function getID()
    {
        // TODO: Implement getID() method.
    }

    /**
     * @return \Object\Design
     */
    public function getDesign()
    {
        return $this->design;
    }

    /**
     * @return \Object\Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @return \Object\Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $ID
     *
     * @return Article
     */
    public function getArticle($ID)
    {
        return $this->articles[$ID];
    }

    /**
     * @param $ID
     *
     * @return Product
     */
    public function getProduct($ID)
    {
        return $this->products[$ID];
    }

    /**
     * @return Article
     */
    public function getFirstArticle()
    {
        //Okay, this is terra-bad, but it's how i'm doing it now.
        //the first article will always be female (unless a female doesn't exist in which case it will pick the first available semi-randomly)
        $productID = null;
        foreach($this->products as $product){
            if($product->getType() == 'female'){
                $productID = $product->getID();
            }
        }
        foreach($this->articles as $article){
            if($article->getProductID() == $productID){
                $returnArticle = $article;
            }
        }
        
        if($productID === null){
            $returnArticle = reset($this->articles);
        }
        return $returnArticle;
    }

    /**
     * @return string
     */
    public function getURLName()
    {
        return urlencode(str_replace(' ', '_', $this->getDesign()->getName()));
    }

    /**
     * @return string
     */
    public function getPermalink(){
        return "//" . $_SERVER['HTTP_HOST'] . '/Item/' . $this->getURLName();
    }
}