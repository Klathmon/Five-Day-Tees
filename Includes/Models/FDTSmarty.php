<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/25/13
 */
require('Library/Smarty/3.1.14/Smarty.class.php');

/**
 * Class FDTSmarty
 *
 * This is the "Smarty" class. This one is a bare bones version with no default formatting included
 */
class FDTSmarty extends Smarty
{
    /** @var ConfigParser */
    private $config;
    /** @var string */
    private $template;

    private $cacheID;

    private $cssSheets;
    private $javascripts;

    /**
     * Set the template that you want to display
     *
     * @param ConfigParser $config
     * @param string       $template
     * @param string       $title
     * @param string       $cacheID
     */
    public function __construct($config, $template, $title = '', $cacheID = null)
    {
        parent::__construct();

        $this->config   = $config;
        $this->cacheID  = $cacheID;
        $this->template = $template;

        $this->assign('title', $title);

        $this->setTemplateDir(get_include_path() . 'Views/');
        $this->setCompileDir('Cache/CompiledTemplates');
        $this->setCacheDir('Cache/CachedTemplates');

        if ($this->config->get('DEBUG', 'DEBUGGING')) {
            //$this->debugging = true;
        } else {
            $this->debugging = false;
        }


        if ($this->config->get('DEBUG', 'FORCE_RECOMPILE')) {

            //force recompile is on, don't use any caching and force a compile each time
            $this->force_compile  = true;
            $this->compile_check  = true;
            $this->cache_lifetime = 0;
            $this->caching        = Smarty::CACHING_OFF;

        } elseif ($this->config->get('DEBUG', 'STATIC_CACHING') && !is_null($this->cacheID)) {

            //Conditions are right, use caching!
            $this->force_compile  = false;
            $this->compile_check  = false;
            $this->cache_lifetime = 3600; //One hour
            $this->caching        = Smarty::CACHING_LIFETIME_CURRENT;

        } else {

            //Either caching is turned off, or the $cacheID is not set, so use normal compilation
            $this->force_compile  = false;
            $this->compile_check  = true;
            $this->cache_lifetime = 0;
            $this->caching        = Smarty::CACHING_OFF;
            $this->cacheID        = null;

        }
    }

    public function isPageCached()
    {
        return $this->isCached($this->template, $this->cacheID);
    }

    /**
     * Displays the template with all of it's settings.
     */
    public function output()
    {
        $this->assign('javascripts', $this->javascripts);
        $this->assign('cssSheets', $this->cssSheets);
        $this->assign('config', $this->config);

        header('Content-type: text/html; charset=UTF-8'); //Send the Content-type header and charset.

        //Display the template!


        if (is_null($this->cacheID)) {
            parent::display($this->template);
        } else {
            parent::display($this->template, $this->cacheID);
        }
    }

    /**
     * Adds the given Javascript file to the template (in the header)
     *
     * @param mixed $scripts
     */
    public function addJs($scripts)
    {
        foreach ((array)$scripts as $script) {
            $this->javascripts[] = '/Static/JS/' . $script;
        }
    }

    /**
     * Adds the given CSS Sheets into the template (in the header)
     *
     * @param mixed $sheets
     */
    public function addCss($sheets)
    {
        foreach ((array)$sheets as $sheet) {
            $this->cssSheets[] = '/Static/CSS/' . $sheet;
        }
    }
}