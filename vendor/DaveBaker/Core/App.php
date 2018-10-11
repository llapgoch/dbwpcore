<?php

namespace DaveBaker\Core;
/**
 * Class App
 * @package DaveBaker\Core
 */
class App
{
    const DEFAULT_OBJECT_MANAGER = '\DaveBaker\Core\Object\Manager';

    /** @var array */
    protected static $apps = [];
    /** @var string */
    protected $namespace;
    /** @var \DaveBaker\Core\Controller\Manager */
    protected $controllerManager;
    /** @var Page\Manager */
    protected $pageManager;
    /** @var Option\Manager */
    protected $optionManager;
    /** @var \DaveBaker\Core\Installer\ManagerInterface object */
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
    /** @var  \DaveBaker\Core\App\Response */
    protected $response;
    /** @var  \DaveBaker\Core\Config\ConfigInterface */
    protected $generalConfig;
    /** @var \DaveBaker\Core\Main\MainInterface  */
    protected $main;
    /** @var  \DaveBaker\Core\Object\Manager */
    protected $objectManager;
    /** @var \DaveBaker\Core\App\Registry */
    protected $registry;
    /** @var \DaveBaker\Core\Session\SessionInterface */
    protected $generalSession;
    /** @var \DaveBaker\Core\Api\Manager */
    protected $apiManager;
    /** @var bool  */
    protected $applicationInitialised = false;
    /** @var string  */
    protected $scriptPrefix = 'dbwpcore_';

    public function __construct(
        $namespace,
        $mainClassName,
        $objectManagerClassName,
        $objectManagerConfigClassName
    ) {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $wpdb->show_errors(false);

        setlocale(LC_ALL, get_locale() . 'utf8', get_locale());

        $this->namespace = $namespace . "_";
        $this->objectManager = new $objectManagerClassName($this, new $objectManagerConfigClassName);

        $this->registerApp($this->namespace, $this);
        $this->main = $this->getObjectManager()->createAppObject($mainClassName);

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
    public static function registerApp($namespace, App $app)
    {
        self::$apps[$namespace] = $app;
    }

    /**
     * @throws Object\Exception
     */
    protected function addScripts()
    {
        $urlHelper = $this->getHelper('Url');

        wp_enqueue_script(
            "{$this->scriptPrefix}block_replacer",
            $urlHelper->getPluginUrl('assets/vendor/dbwpcore/js/block-replacer.js'),
            ['jquery']
        );

        wp_enqueue_script("{$this->scriptPrefix}block_replacer");
        wp_enqueue_script("jank_mustache");

        wp_register_script(
            "{$this->scriptPrefix}table_updater",
            $urlHelper->getPluginUrl('assets/vendor/dbwpcore/js/table.updater.widget.js'),
            ['jquery', 'jquery-ui-widget']
        );

        wp_register_script(
            "{$this->scriptPrefix}file_uploader",
            $urlHelper->getPluginUrl('assets/vendor/dbwpcore/js/file.uploader.widget.js'),
            ['jquery', 'jquery-ui-widget']
        );

    }

    /**
     * @return $this
     */
    protected function addEvents()
    {
        add_action('init', function(){
            // Register the core installer before any other installers
            // TODO: Change this so multiples can be registered
            $this->getInstallerManager()->register([
                '\DaveBaker\Core\Installer\CoreApplication',
                '\DaveBaker\Core\Installer\CoreDirectory'
            ]);

            $this->getMain()->registerInstallers();
            $this->install();
        });

        /*  We have to do initLayout in multiple actions because not all actions exist on every page.
            This may need adding to
            TODO: ADD THIS TO ACTION ON REST CALLS
        */

        add_action('init', function(){
            // Init API Manager to add events
            $this->initManagers();
        });

        add_action('wp', function(){
            $this->initApplication();
        });

        add_action('login_init', function(){
            $this->initApplication();
        });

        add_action('rest_api_init', function(){
            $this->registerApiActions();
            $this->getMain()->registerApiActions();
            $this->getApiManager()->registerRoutes();
        });

        add_action('rest_request_before_callbacks', function(){
            $this->initApplication();
        });

        add_action('shutdown', function(){
            $this->getLayoutManager()->postDispatch();
            $this->getContollerManager()->postDispatch();
        });

        return $this;
    }

    /**
     * @throws Object\Exception
     * @return $this
     */
    protected function registerApiActions()
    {
        $this->getApiManager()->addRoute(
            'core/file',
            '\DaveBaker\Core\Api\Core\File'
        );

        return $this;
    }

    /**
     * @return $this
     * @throws Api\Exception
     * @throws Controller\Exception
     * @throws Event\Exception
     * @throws Layout\Exception
     * @throws Model\Db\Exception
     * @throws Object\Exception
     */
    protected function initApplication()
    {
        if($this->applicationInitialised){
            return $this;
        }

        $this->applicationInitialised = true;

        $this->addScripts();

        $this->getHandleManager()->registerHandles();
        $this->getMain()->registerControllers();
        $this->getMain()->registerLayouts();

        $this->getContollerManager()->preDispatch();
        $this->getLayoutManager()
            ->runLayouts()
            ->registerShortcodes()
            ->registerActions()
            ->preDispatch();

        $this->getContollerManager()->execute();

        return $this;
    }

    /**
     * @throws App\Exception
     * @throws Object\Exception
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
     * @return mixed
     * @throws Object\Exception
     */
    public function getHelper($helperName)
    {
        return $this->getObjectManager()->getHelper($helperName);
    }

    /**
     * @return Config\ConfigInterface
     * @throws Object\Exception
     */
    public function getGeneralConfig()
    {
        if(!$this->generalConfig){
            $this->generalConfig = $this->getObjectManager()->get('\DaveBaker\Core\Config\General');
        }

        return $this->generalConfig;
    }

    /**
     * @throws App\Exception
     * @throws Object\Exception
     * @return $this
     */
    public function initManagers()
    {
        $this->getInstallerManager();
        $this->getEventManager();
        $this->getHandleManager();
        $this->getContollerManager();
        $this->getApiManager();
        $this->getLayoutManager();
        $this->getPageManager();
        $this->getBlockManager();
        $this->getRequest();
        $this->getResponse();
        $this->getOptionManager();

        return $this;
    }

    /**
     * @return Block\Manager|object
     * @throws Object\Exception
     */
    public function getBlockManager()
    {
        if(!$this->blockManager){
            $this->blockManager = $this->getObjectManager()->getAppObject('\DaveBaker\Core\Block\Manager');
        }

        return $this->blockManager;
    }

    /**
     * @return Event\Manager|object
     * @throws Object\Exception
     */
    public function getEventManager()
    {
        if(!$this->eventManager){
            $this->eventManager = $this->getObjectManager()->getAppObject('\DaveBaker\Core\Event\Manager');
        }

        return $this->eventManager;
    }

    /**
     * @return Page\Manager|object
     * @throws Object\Exception
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
     * @return App\Registry|object
     * @throws Object\Exception
     */
    public function getRegistry()
    {
        if(!$this->registry){
            $this->registry = $this->getObjectManager()->get(
                '\DaveBaker\Core\App\Registry'
            );
        }

        return $this->registry;
    }

    /**
     * @return \DaveBaker\Core\Object\Manager
     * This is created in the constructor
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return Layout\Manager|object
     * @throws Object\Exception
     */
    public function getLayoutManager()
    {
        if(!$this->layoutManager){
            $this->layoutManager = $this->getObjectManager()->getAppObject(
                '\DaveBaker\Core\Layout\Manager',
                [$this->getObjectManager()->get('\DaveBaker\Core\Config\Layout')]
            );
        }

        return $this->layoutManager;
    }

    /**
     * @return Layout\Handle\Manager|object
     * @throws Object\Exception
     */
    public function getHandleManager()
    {
        if(!$this->handleManager) {
            $this->handleManager = $this->getObjectManager()->getAppObject('\DaveBaker\Core\Layout\Handle\Manager');
        }

        return $this->handleManager;
    }


    /**
     * @return Installer\ManagerInterface
     * @throws App\Exception
     * @throws Object\Exception
     */
    public function getInstallerManager()
    {
        if(!$this->installerManager){
            $this->installerManager = $this->getObjectManager()->getAppObject('\DaveBaker\Core\Installer\Manager');

            if(!$this->installerManager instanceof \DaveBaker\Core\Installer\ManagerInterface){
                throw new \DaveBaker\Core\App\Exception("Installer Manager must implement ManagerInterface");
            }
        }
        return $this->installerManager;
    }

    /**
     * @return App\Request|object
     * @throws Object\Exception
     */
    public function getRequest()
    {
        if(!$this->request){
            $this->request = $this->getObjectManager()->getAppObject('\DaveBaker\Core\App\Request');
        }

        return $this->request;
    }

    /**
     * @return App\Response|object
     * @throws Object\Exception
     */
    public function getResponse()
    {
        if(!$this->response){
            $this->response = $this->getObjectManager()->getAppObject('\DaveBaker\Core\App\Response');
        }

        return $this->response;
    }

    /**
     * @return Option\Manager|object
     * @throws Object\Exception
     */
    public function getOptionManager()
    {
        if(!$this->optionManager){
            $this->optionManager = $this->objectManager->getAppObject('\DaveBaker\Core\Option\Manager');
        }

        return $this->optionManager;
    }

    /**
     * @return Controller\Manager|object
     * @throws Object\Exception
     */
    public function getContollerManager()
    {
        if(!$this->controllerManager){
            $this->controllerManager = $this->getObjectManager()->getAppObject('\DaveBaker\Core\Controller\Manager');
        }

        return $this->controllerManager;
    }

    /**
     * @return \DaveBaker\Core\Api\Manager
     * @throws Object\Exception
     */
    public function getApiManager()
    {
        if(!$this->apiManager){
            $this->apiManager = $this->getObjectManager()->getAppObject('\DaveBaker\Core\Api\Manager');
        }

        return $this->apiManager;
    }

    /**
     * @return \DaveBaker\Core\Session\General|object
     * @throws Object\Exception
     */
    public function getGeneralSession()
    {
        if(!$this->generalSession){
            $this->generalSession = $this->getObjectManager()->getAppObject('\DaveBaker\Core\Session\General');
        }

        return $this->generalSession;
    }
}