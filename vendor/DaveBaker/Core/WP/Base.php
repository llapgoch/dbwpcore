<?php

namespace DaveBaker\Core\WP;

abstract class Base
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
    protected $namespaceCode = 'default_';

    public function __construct(
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\WP\Option\Manager $optionManager = null
    ) {
        $this->app = $app;

        if(!$optionManager){
            $optionManager = $this->app->getObjectManager()->get(
                '\DaveBaker\Core\WP\Option\Manager',
                [$this->getOptionNamespace()]
            );
        }

        $this->optionManager = $optionManager;
    }

    /**
     * @param $event string
     * @param array $params
     */
    public function fireEvent($event, $params = [])
    {
        $this->getApp()->getEventManager()->fireEvent(
            $this->getNamespacedEvent($event),
            $params
        );
    }

    /**
     * @return \DaveBaker\Core\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param $event string
     * @return string
     */
    public function getNamespacedEvent($event)
    {
        return $this->namespaceCode . $event;
    }

    /**
     * @return string
     */
    public function getOptionNamespace()
    {
        return $this->getApp()->getNamespace() . $this->namespaceCode;
    }

    /**
     * @return \DaveBaker\Core\WP\Option\Manager
     */
    protected function getOptionManager()
    {
        return $this->optionManager;
    }

    /**
     * @return Event\Manager
     */
    protected function getEventManager()
    {
        return $this->getApp()->getEventManager();
    }
}