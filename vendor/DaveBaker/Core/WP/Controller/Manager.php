<?php

/**
 * Example for listening to page events
 *
 *   Event = wordpressAction + page_name
 *   $this->getEventManager()->register("wp_hello-world", function(){
 *     var_dump("woo");
 *   });
 */

namespace DaveBaker\Core\WP\Controller;

class Manager extends \DaveBaker\Core\WP\Base
{
    /** @var  \WP_Post */
    protected $post;
    
    /** @var string */
    protected $namespaceCode = "controller";
    protected $controllers = [];

    public function __construct(
        \DaveBaker\Core\App $app
    ){
        parent::__construct($app);
    }

    /**
     * @param $controllers array
     * @return $this
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    public function register($controllers)
    {
        /**
         * @var  $handle string
         * @var  $controllerClass string
         */
        foreach($controllers as $handle => $controllerClass){
            $this->registerController($handle, $controllerClass);
        }

        return $this;
    }

    public final function preDispatch()
    {
        $this->_preDispatch();

        $handles = $this->getApp()->getHandleManager()->getHandles();

        foreach($handles as $handle){
            /** @var \DaveBaker\Core\WP\Controller\ControllerInterface $controller */
            foreach($this->getControllersForHandle($handle) as $controller){
                $controller->preDispatch();
            }
        }

        return $this;
    }

    public function execute()
    {
        $handles = $this->getApp()->getHandleManager()->getHandles();

        foreach($handles as $handle){
            /** @var \DaveBaker\Core\WP\Controller\ControllerInterface $controller */
            foreach($this->getControllersForHandle($handle) as $controller){
                $controller->execute();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public final function postDispatch()
    {
        $this->_postDispatch();
        $handles = $this->getApp()->getHandleManager()->getHandles();

        foreach($handles as $handle){
            /** @var \DaveBaker\Core\WP\Controller\ControllerInterface $controller */
            foreach($this->getControllersForHandle($handle) as $controller){
                $controller->postDispatch();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _preDispatch()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _postDispatch()
    {
        return $this;
    }

    /**
     * @param $handle string
     * @param $controllerClass string
     * @throws Exception
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    protected function registerController(
        $handle,
        $controllerClass
    ) {
        if(!isset($this->controllers[$handle])){
            $this->controllers[$handle] = [];
        }

        if(isset($this->controllers[$handle][$controllerClass])){
            return;
        }

        $controller = $this->getApp()->getObjectManager()->get($controllerClass, [$this->getApp()]);

        if(!$controller instanceof \DaveBaker\Core\WP\Controller\ControllerInterface){
            throw new Exception("Controller is not compatible with ControllerInterface");
        }

        $this->controllers[$handle][$controllerClass] = $controller;
    }

    /**
     * @param $handle
     * @return array
     */
    protected function getControllersForHandle($handle)
    {
        if(isset($this->controllers[$handle])){
            return $this->controllers[$handle];
        }

        return [];
    }
}