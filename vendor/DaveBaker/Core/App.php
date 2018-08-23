<?php

namespace DaveBaker\Core;

class App
{
    const DEFAULT_OBJECT_MANAGER = '\DaveBaker\Core\Object\Manager';

    protected static $apps = [];
    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var \DaveBaker\Core\Controller\Manager
     */
    protected $controllerManager;
    /**
     * @var Page\Manager
     */
    protected $pageManager;
    /**
     * @var Option\Manager
     */
    protected $optionManager;

    /** @var \DaveBaker\Core\Installer\InstallerInterface object */
    protected $installerManager;

    /** @var \DaveBaker\Core\Block\Manager */
    protected $blockManager;

    /** @var \DaveBaker\Core\Layout\Manager */
    protected $layoutManager;

    /** @var  \DaveBaker\Core\Layout\Handle\Manager */
    protected $handleManager;

    /** @var  \DaveBaker\Core\Event\Manager */
    protected $eventManager;

    /** @var  \DaveBaker\Core\App\Request */
    protected $request;


    /** @var \DaveBaker\Core\Main\MainInterface  */
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
     * @throws App\Exception
     */
    public static function getApp($namespace = '')
    {
        if(count(self::$apps) == 1 && !$namespace){
            return array_values(self::$apps)[0];
        }

        if(!isset(self::$apps[$namespace])){
            throw new \DaveBaker\Core\App\Exception("App not registered {$namespace}");
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
     * @throws Installer\Exception
     */
    protected function install()
    {
        $this->getInstallerManager()->checkInstall();
    }

    /**
     * @return Main\MainInterface
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
     * @return Block\Manager
     */
    public function getBlockManager()
    {
        if(!$this->blockManager){
            $this->blockManager = $this->getObjectManager()->get('\DaveBaker\Core\Block\Manager', [$this]);
        }

        return $this->blockManager;
    }

    /**
     * @return Event\Manager
     */
    public function getEventManager()
    {
        if(!$this->eventManager){
            $this->eventManager = $this->getObjectManager()->get('\DaveBaker\Core\Event\Manager', [$this]);
        }

        return $this->eventManager;
    }

    /**
     * @return Page\Manager
     */
    public function getPageManager()
    {
        if(!$this->pageManager){
            $this->pageManager = $this->getObjectManager()->get(
                '\DaveBaker\Core\Page\Manager',
                [$this, $this->getObjectManager()->get('\DaveBaker\Core\Config\Page')]
            );
        }

        return $this->pageManager;
    }

    /**
     * @return \DaveBaker\Core\Object\Manager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return Layout\Manager
     */
    public function getLayoutManager()
    {
        if(!$this->layoutManager){
            $this->layoutManager = $this->getObjectManager()->get(
                '\DaveBaker\Core\Layout\Manager',
                [$this, $this->getObjectManager()->get('\DaveBaker\Core\Config\Layout')]
            );
        }

        return $this->layoutManager;
    }

    /**
     * @return Layout\Handle\Manager
     */
    public function getHandleManager()
    {
        if(!$this->handleManager) {
            $this->handleManager = $this->handleManager = $this->getObjectManager()->get('\DaveBaker\Core\Layout\Handle\Manager', [$this]);
        }

        return $this->handleManager;
    }


    /**
     * @return Installer\InstallerInterface
     * @throws App\Exception
     * @throws Object\Exception
     */
    public function getInstallerManager()
    {
        if(!$this->installerManager){
            $this->installerManager = $this->getObjectManager()->get('\DaveBaker\Core\Installer\Manager', [$this]);

            if(!$this->installerManager instanceof \DaveBaker\Core\Installer\InstallerInterface){
                throw new \DaveBaker\Core\App\Exception("Installer Manager must implement InstallerInterface");
            }
        }
        return $this->installerManager;
    }

    /**
     * @return \DaveBaker\Core\App\Request
     */
    public function getRequest()
    {
        if(!$this->request){
            $this->request = $this->getObjectManager()->get('\DaveBaker\Core\App\Request', [$this]);
        }

        return $this->request;
    }

    /**
     * @return Option\Manager
     */
    public function getOptionManager()
    {
        if(!$this->optionManager){
            $this->optionManager = $this->objectManager->get('\DaveBaker\Core\Option\Manager', [$this]);
        }

        return $this->optionManager;
    }

    /**
     * @return Controller\Manager|object
     */
    public function getContollerManager()
    {
        if(!$this->controllerManager){
            $this->controllerManager = $this->getObjectManager()->get('\DaveBaker\Core\Controller\Manager', [$this]);
        }

        return $this->controllerManager;
    }

}