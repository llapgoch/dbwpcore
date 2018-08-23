<?php

namespace DaveBaker\Core;

abstract class Base
{
    /** @var \DaveBaker\Core\App  */
    protected $app;
    /**
     * @var \DaveBaker\Core\Option\Manager
     */
    protected $optionManager;

    /** @var  \wpdb */
    protected $wpdb;

    /**
     * @var string
     */
    protected $namespaceCode = 'default_';

    public function __construct(
        \DaveBaker\Core\App $app
    ) {
        $this->app = $app;

        global $wpdb;
        $this->wpdb = $wpdb;

        $this->_construct();
    }

    /**
     * @return $this
     */
    protected function _construct()
    {
        return $this;
    }



    /**
     * @param $event
     * @param $callback
     * @param bool $allowMultiples
     * @return $this
     */
    public function addEvent($event, $callback, $allowMultiples = false)
    {
        $this->getApp()->getEventManager()->add(
            $event,
            $callback,
            $allowMultiples
        );

        return $this;
    }

    /**
     * @param $event string
     * @param array $params
     * @return $this
     */
    public function fireEvent($event, $params = [])
    {
        $params['object'] = $this;

        return $this->getApp()->getEventManager()->fire(
            $this->getNamespacedEvent($event),
            $params
        );
        
    }

    /**
     * @param $event string
     * @param $callback object|bool
     * @return $this
     */
    public function removeEvent($event, $callback = false)
    {
        $this->getApp()->getEventManager()->remove($event, $callback);
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
     * @return \wpdb
     */
    public function getDb()
    {
        return $this->wpdb;
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
     * @return \DaveBaker\Core\Option\Manager
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