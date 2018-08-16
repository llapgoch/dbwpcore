<?php

namespace DaveBaker\Core\WP;

class Base
{
    /** @var \DaveBaker\Core\App  */
    protected $app;
    protected $optionManager;

    /**
     * @var string
     */
    protected $namespaceSuffix = 'default_';

    public function __construct(
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\WP\Option\Manager $optionManager = null
    ) {
        $this->app = $app;
        
        if(!$optionManager){
            $className = $this->app->getObjectManager()->getDefaultClassName('\DaveBaker\Core\WP\Option\Manager');
            $this->optionManager = new $className($this->getNamespace());
        }
    }

    /**
     * @return \DaveBaker\Core\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->app->getNamespace() . $this->namespaceSuffix;
    }
}