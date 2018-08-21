<?php

namespace DaveBaker\Core;

class App
{
    const DEFAULT_OBJECT_MANAGER = '\DaveBaker\Core\WP\Object\Manager';
    const GENERAL_NAMESPACE_SUFFIX = 'general_';

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
    protected $generalOptionManager;

    /** @var \DaveBaker\Core\WP\Installer\Manager object */
    protected $installerManager;

    /** @var \DaveBaker\Core\WP\Block\Manager */
    protected $blockManager;

    /** @var \DaveBaker\Core\WP\Layout\Manager */
    protected $layoutManager;


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
            [
                $this,
                null,
                $this->getObjectManager()->get('\DaveBaker\Core\WP\Config\Page')
            ]
        );

        $this->installerManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Installer\Manager', [$this]);
        $this->controller = $this->getObjectManager()->get('\DaveBaker\Core\WP\Controller\Front', [$this]);
        $this->blockManager = $this->getObjectManager()->get('\DaveBaker\Core\WP\Block\Manager', [$this]);
        $this->layoutManager = $this->getObjectManager()->get(
            '\DaveBaker\Core\WP\Layout\Manager',
            [
                $this,
                null,
                $this->getObjectManager()->get('\DaveBaker\Core\WP\Config\Layout')
            ]);


        /** @var  generalOptionManager
         * A general store for options, local versions of the option manager should be used for
         * More localised namespacing
         */
        $this->generalOptionManager = $this->objectManager->get(
            '\DaveBaker\Core\WP\Option\Manager',
            [$this->getNamespace() . self::GENERAL_NAMESPACE_SUFFIX]
        );

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
        
        add_action('wp', function(){
            $this->getLayoutManager()->registerHandles();
            $this->getMain()->registerLayouts();
            $this->getLayoutManager()->registerShortcodes();
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
     * @return WP\Option\Manager
     *
     * Only use for general options, use more specifically namespaced versions for other options
     */
    public function getGeneralOptionManager()
    {
        return $this->generalOptionManager;
    }

}