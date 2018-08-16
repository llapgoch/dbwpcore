<?php

namespace DaveBaker\Core;

class App
{
    const DEFAULT_OBJECT_MANAGER = '\DaveBaker\Core\WP\Object\Manager';
    const GENERAL_NAMESPACE_SUFFIX = 'general_';
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

    /** @var \DaveBaker\Core\WP\Installer\Manager object */
    protected $installerManager;

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
        $this->objectManager = $objectManager;

        if(!$objectManager){
            $manager = self::DEFAULT_OBJECT_MANAGER;
            $this->objectManager = new $manager();
        }

        // For any singleton objects, they'll be stored against the namespace, allowing for multiple
        // singletons across different app definitions
        $this->objectManager->setNamespace($this->getNamespace());
        $this->pageManager = $this->objectManager->get('\DaveBaker\Core\WP\Page\Manager', [$this]);
        $this->installerManager = $this->objectManager->get('\DaveBaker\Core\WP\Installer\Manager', [$this]);


        /** @var  generalOptionManager
         * A general store for options, local versions of the option manager should be used for
         * More localised namespacing
         */
        $this->generalOptionManager = $this->objectManager->get(
            '\DaveBaker\Core\WP\Option\Manager',
            [$this->getNamespace() . self::GENERAL_NAMESPACE_SUFFIX]
        );

        $this->install();
    }

    /**
     * @throws WP\Installer\Exception
     */
    protected function install()
    {
        $this->installerManager->checkInstall();
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
     *
     * Only use for general options, use more specifically namespaced versions for other options
     */
    public function getGeneralOptionManager()
    {
        return $this->generalOptionManager;
    }

}