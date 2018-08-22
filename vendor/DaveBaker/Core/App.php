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
     * @var \DaveBaker\Core\WP\Controller\Manager
     */
    protected $controllerManager;
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
        $mainClassName,
        $objectManagerClassName,
        $objectManagerConfigClassName
    ) {

        $this->namespace = $namespace . "_";
        $this->main = new $mainClassName($this);
        $this->objectManager = new $objectManagerClassName($this, new $objectManagerConfigClassName);

        $this->registerApp($this->namespace, $this);
        $this->main->setApp($this);

        // For any singleton objects, they'll be stored against the namespace, allowing for multiple
        // singletons across different app definitions
        $this->objectManager->setNamespace($this->getNamespace());

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

    /**
     * @return $this
     */
    protected function addEvents()
    {
        add_action('init', function(){
            $this->install();
        });

        /*  We have to do initLayout in multiple actions because not all actions exist on every page.
            This may need adding to */
        add_action('wp', function(){
            $this->initApplication();
        });


        add_action('login_init', function(){
            $this->initApplication();
        });

        add_action('shutdown', function(){
            $this->getLayoutManager()->postDispatch();
            $this->getContollerManager()->postDispatch();
        });

        return $this;
    }

    /**
     * @return $this
     */
    protected function initApplication()
    {
        $this->getHandleManager()->registerHandles();

        $this->getMain()->registerControllers();

        $this->getMain()->registerLayouts();
        
        $this->getContollerManager()->preDispatch();
        $this->getLayoutManager()->registerShortcodes()->registerActions()->preDispatch();
        $this->getContollerManager()->execute();

        return $this;
    }

    /**
     * @throws WP\Installer\Exception
     */
    protected function install()
    {
        $this->getInstallerManager()->checkInstall();
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
     * @return \DaveBaker\Core\WP\Helper\Base
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
        if(!$this->blockManager){
            $this->blockManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Block\Manager', [$this]);
        }

        return $this->blockManager;
    }

    /**
     * @return WP\Event\Manager
     */
    public function getEventManager()
    {
        if(!$this->eventManager){
            $this->eventManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Event\Manager', [$this]);
        }

        return $this->eventManager;
    }

    /**
     * @return WP\Page\Manager
     */
    public function getPageManager()
    {
        if(!$this->pageManager){
            $this->pageManager = $this->getObjectManager()->get(
                '\DaveBaker\Core\WP\Page\Manager',
                [$this, $this->getObjectManager()->get('\DaveBaker\Core\WP\Config\Page')]
            );
        }

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
        if(!$this->layoutManager){
            $this->layoutManager = $this->getObjectManager()->get(
                '\DaveBaker\Core\WP\Layout\Manager',
                [$this, $this->getObjectManager()->get('\DaveBaker\Core\WP\Config\Layout')]
            );
        }

        return $this->layoutManager;
    }

    /**
     * @return WP\Layout\Handle\Manager
     */
    public function getHandleManager()
    {
        if(!$this->handleManager) {
            $this->handleManager = $this->handleManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Layout\Handle\Manager', [$this]);
        }

        return $this->handleManager;
    }


    /**
     * @return WP\Installer\InstallerInterface
     * @throws WP\App\Exception
     * @throws WP\Object\Exception
     */
    public function getInstallerManager()
    {
        if(!$this->installerManager){
            $this->installerManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Installer\Manager', [$this]);

            if(!$this->installerManager instanceof \DaveBaker\Core\WP\Installer\InstallerInterface){
                throw new \DaveBaker\Core\WP\App\Exception("Installer Manager must implement InstallerInterface");
            }
        }
        return $this->installerManager;
    }

    /**
     * @return \DaveBaker\Core\WP\App\Request
     */
    public function getRequest()
    {
        if(!$this->request){
            $this->request = $this->getObjectManager()->get('\DaveBaker\Core\WP\App\Request', [$this]);
        }

        return $this->request;
    }

    /**
     * @return WP\Option\Manager
     */
    public function getOptionManager()
    {
        if(!$this->optionManager){
            $this->optionManager = $this->objectManager->get('\DaveBaker\Core\WP\Option\Manager', [$this]);
        }

        return $this->optionManager;
    }

    /**
     * @return WP\Controller\Manager|object
     */
    public function getContollerManager()
    {
        if(!$this->controllerManager){
            $this->controllerManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Controller\Manager', [$this]);
        }

        return $this->controllerManager;
    }

}