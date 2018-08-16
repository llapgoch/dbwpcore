<?php

namespace DaveBaker\Core\WP\Page;

class Manager extends \DaveBaker\Core\WP\Base
{
    protected $pageCache = [];
    protected $namespaceSuffix = "page_";
    
    public function createPage(
        $pageIdentifier,
        $overwrite = false)
    {
        $namespacedId = $this->getNamespace($pageIdentifier);

    }

    public function pageExists(
        $pageIdentifier
    ) {
        if($page = $this->retreiveFromCache($pageIdentifier)){
            return $page;
        }

        return $this->getPage($pageIdentifier);
    }

    public function getPage($pageIdentifier, $reload = false)
    {
        $namespacedId = $this->app->getNamespacedOption($pageIdentifier);
        
        if($reload || !($page = $this->retreiveFromCache($pageIdentifier))){
            $post = get_post($namespacedId);
        }
    }

    /**
     * @param $pageIdentifier
     * @return mixed|null
     */
    protected function retreiveFromCache($pageIdentifier)
    {
        $option = $this->getApp()->getNamespacedOption($pageIdentifier);

        if(isset($this->pageCache[$option])){
            return $this->pageCache[$option];
        }

        return null;
    }
    
}