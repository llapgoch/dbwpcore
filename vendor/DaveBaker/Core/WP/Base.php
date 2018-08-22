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
        \DaveBaker\Core\App $app
    ) {
        $this->app = $app;
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
     * @param $event
     * @param $callback object
     * @return $this
     */
    public function addEvent($event, $callback)
    {
        $this->getApp()->getEventManager()->addEvent(
            $this->getNamespacedEvent($event),
            $callback
        );

        return $this;
    }

    /**
     * @return \DaveBaker\Core\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param $optionCode string
     * @param $value mixed
     * @return $this
     */
    public function setOption($optionCode, $value)
    {
        $this->getOptionManager()->set($this->getNamespacedOption($optionCode), $value);
        return $this;
    }

    /**
     * @param $optionCode string
     * @return mixed|void
     */
    public function getOption($optionCode)
    {
        return $this->getOptionManager()->get($this->getNamespacedOption($optionCode));
    }

    /**
     * @param $event string
     * @return string
     *
     * Returns, for example "layout_create"
     */
    public function getNamespacedEvent($event)
    {
        return $this->namespaceCode . "_" . $event;
    }

    /**
     * @param $optionCode string
     * @return string
     *
     * For registering namespaced options for the application, which are stored using Wordpress' option system
     * key in the database, for example, would be "applicationname_installer_version"
     */
    public function getNamespacedOption($optionCode)
    {
        return $this->getApp()->getNamespace() . "_" . $this->namespaceCode . "_" . $optionCode;
    }

    /**
     * @return \DaveBaker\Core\WP\Option\Manager
     */
    protected function getOptionManager()
    {
        return $this->getApp()->getOptionManager();
    }

    /**
     * @return Event\Manager
     */
    protected function getEventManager()
    {
        return $this->getApp()->getEventManager();
    }
}