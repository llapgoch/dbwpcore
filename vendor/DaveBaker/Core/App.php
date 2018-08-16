<?php

namespace DaveBaker\Core;

class App
{
    const DEFAULT_OBJECT_MANAGER = '\DaveBaker\Core\WP\Object\Manager';

    protected $generalNamespaceSuffix = 'default_';
    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var Controller\Front
     */
    protected $controller;
    /**
     * @var WP\Page\Manager
     */
    protected $pageManager;
    /**
     * @var WP\Option\Manager
     */
    protected $generalOptionManager;

    /**
     * @var
     */
    protected $objectManager;

    public function __construct(
        $namespace,
        \DaveBaker\Core\WP\Object\Manager $objectManager = null
    ) {
        $this->namespace = $namespace . "_";
        $this->controller = new Controller\Front($this);
        $this->pageManager = new WP\Page\Manager($this);
        $this->objectManager = $objectManager;
        
        if(!$objectManager){
            var_dump(self::DEFAULT_OBJECT_MANAGER);exit;
            $this->objectManager = new self::$DEFAULT_OBJECT_MANAGER();
        }

        /** @var  generalOptionManager
         * A general store for options, local versions of the option manager should be used for
         * More localised namespacing
         */
//        $this->generalOptionManager = new WP\Option\Manager(DEFAULT_NAMESPACE . $this->generalNamespaceSuffix);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return WP\Page\Manager
     */
    public function getPageManager()
    {
        return $this->pageManager;
    }

    /**
     * @return \DaveBaker\Core\WP\Object\Manager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return WP\Option\Manager
     */
    public function getGeneralOptionManager()
    {
        return $this->generalOptionManager;
    }

}