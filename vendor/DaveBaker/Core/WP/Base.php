<?php

namespace DaveBaker\Core\WP;

class Base
{
    /** @var \DaveBaker\Core\App  */
    protected $app;
    /**
     * @var \DaveBaker\Core\WP\Option\Manager
     */
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
            $this->optionManager = $this->app->getObjectManager()->get(
                '\DaveBaker\Core\WP\Option\Manager',
                [$this->namespaceSuffix]
            );
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

    /**
     * @return \DaveBaker\Core\WP\Option\Manager
     */
    protected function getOptionManager()
    {
        return $this->optionManager;
    }
}