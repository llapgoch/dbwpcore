<?php

namespace DaveBaker\Core;

class App
{
    const DEFAULT_OBJECT_MANAGER = '\DaveBaker\Core\WP\Object\Manager';

    protected static $apps = [];
    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var \DaveBaker\Core\WP\Controller\Front
     */
    protected $controller;
    /**
     * @var WP\Page\Manager
     */
    protected $pageManager;
    /**
     * @var WP\Option\Manager
     */
    protected $optionManager;

    /** @var \DaveBaker\Core\WP\Installer\InstallerInterface object */
    protected $installerManager;

    /** @var \DaveBaker\Core\WP\Block\Manager */
    protected $blockManager;

    /** @var \DaveBaker\Core\WP\Layout\Manager */
    protected $layoutManager;

    /** @var  \DaveBaker\Core\WP\Layout\Handle\Manager */
    protected $handleManager;

    /** @var  \DaveBaker\Core\WP\Event\Manager */
    protected $eventManager;

    /** @var  \DaveBaker\Core\WP\App\Request */
    protected $request;


    /** @var \DaveBaker\Core\WP\Main\MainInterface  */
    protected $main;

    /**
     * @var
     */
    protected $objectManager;

    public function __construct(
        $namespace,
        \DaveBaker\Core\WP\Main\MainInterface $main,
        \DaveBaker\Core\WP\Object\Manager $objectManager = null
    ) {
        $this->namespace = $namespace . "_";
        $this->objectManager = $objectManager;
        $this->main = $main;
        $this->registerApp($this->namespace, $this);

        $this->main->setApp($this);

        if(!$objectManager){
            $manager = self::DEFAULT_OBJECT_MANAGER;
            $this->objectManager = new $manager();
        }

        // For any singleton objects, they'll be stored against the namespace, allowing for multiple
        // singletons across different app definitions
        $this->objectManager->setNamespace($this->getNamespace());

        $this->pageManager = $this->getObjectManager()->get(
            '\DaveBaker\Core\WP\Page\Manager',
            [$this, $this->getObjectManager()->get('\DaveBaker\Core\WP\Config\Page')]
        );

        $this->installerManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Installer\Manager', [$this]);

        if(!$this->installerManager instanceof \DaveBaker\Core\WP\Installer\InstallerInterface){
            throw new \DaveBaker\Core\WP\App\Exception("Installer Manager must implement InstallerInterface");
        }

        $this->handleManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Layout\Handle\Manager', [$this]);
        $this->eventManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Event\Manager', [$this]);
        $this->controller = $this->getObjectManager()->get('\DaveBaker\Core\WP\Controller\Front', [$this]);
        $this->blockManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Block\Manager', [$this]);
        $this->request = $this->getObjectManager()->get('\DaveBaker\Core\WP\App\Request', [$this]);

        $this->layoutManager = $this->getObjectManager()->get(
            '\DaveBaker\Core\WP\Layout\Manager',
            [$this, $this->getObjectManager()->get('\DaveBaker\Core\WP\Config\Layout')]
        );

        $this->optionManager = $this->objectManager->get('\DaveBaker\Core\WP\Option\Manager');

        $this->getMain()->init();
        $this->addEvents();
    }

    /**
     * @param string $namespace
     * @return mixed
     * @throws WP\App\Exception
     */
    public static function getApp($namespace = '')
    {
        if(count(self::$apps) == 1 && !$namespace){
            return array_values(self::$apps)[0];
        }

        if(!isset(self::$apps[$namespace])){
            throw new \DaveBaker\Core\WP\App\Exception("App not registered {$namespace}");
        }

        return self::$apps[$namespace];
    }

    /**
     * @param $namespace
     * @param App $app
     */
    public static function registerApp(
        $namespace,
        App $app)
    {
        self::$apps[$namespace] = $app;
    }

    protected function addEvents()
    {
        add_action('init', function(){
            $this->install();
            $this->getLayoutManager()->preDispatch();
        });
        
        add_action('wp_loaded', function(){
            $this->getHandleManager()->registerHandles();
            $this->getMain()->registerLayouts();

            $this->getLayoutManager()->registerShortcodes()->registerActions();
        });

        add_action('wp', function(){
            $this->getHandleManager()->registerHandles();
            $this->getMain()->registerLayouts();

            $this->getLayoutManager()->registerShortcodes()->registerActions();
        });

        add_action('shutdown', function(){
            $this->getLayoutManager()->postDispatch();
        });
    }

    /**
     * @throws WP\Installer\Exception
     */
    protected function install()
    {
        $this->installerManager->checkInstall();
    }

    /**
     * @return WP\Main\MainInterface
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param $helperName
     * @return \DaveBaker\Core\Helper\Base
     */
    public function getHelper($helperName)
    {
        return $this->getObjectManager()->getHelper($helperName);
    }

    /**
     * @return WP\Block\Manager
     */
    public function getBlockManager()
    {
        return $this->blockManager;
    }

    /**
     * @return WP\Event\Manager
     */
    public function getEventManager()
    {
        return $this->eventManager;
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
     * @return WP\Layout\Manager
     */
    public function getLayoutManager()
    {
        return $this->layoutManager;
    }

    /**
     * @return WP\Layout\Handle\Manager
     */
    public function getHandleManager()
    {
        return $this->handleManager;
    }

    /**
     * @return \DaveBaker\Core\WP\App\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return WP\Option\Manager
     */
    public function getOptionManager()
    {
        return $this->optionManager;
    }

}