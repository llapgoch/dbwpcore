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
     * @var \DaveBaker\Core\WP\Event\Manager
     */
    protected $eventManager;

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
            $optionManager = $this->app->getObjectManager()->get(
                '\DaveBaker\Core\WP\Option\Manager',
                [$this->getNamespace()]
            );
        }

        $this->optionManager = $optionManager;
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
        return $this->getApp()->getNamespace() . $this->namespaceSuffix;
    }

    /**
     * @return \DaveBaker\Core\WP\Option\Manager
     */
    protected function getOptionManager()
    {
        return $this->optionManager;
    }

    /**
     * @throws Object\Exception
     */
    protected function createEventManager()
    {
        $this->eventManager = $this->getApp()->getObjectManager()->get(
            '\DaveBaker\Core\WP\Event\Manager',
            [$this->getApp(), $this->getOptionManager()]
        );
    }

    /**
     * @return Event\Manager
     */
    protected function getEventManager()
    {
        return $this->eventManager;
    }
}