<?php

namespace DaveBaker\Core\Controller;

use DaveBaker\Core\Definitions\General as GeneralDefinition;
/**
 * Class Manager
 * @package DaveBaker\Core\Controller
 */
class Manager extends \DaveBaker\Core\Base
{
    /** @var  \WP_Post */
    protected $post;
    /** @var string */
    protected $namespaceCode = "controller";
    /** @var array */
    protected $controllers = [];

    /**
     * @param $controllers
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
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

    /**
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function preDispatch()
    {
        $this->fireEvent('predispatch_before');
        $this->_preDispatch();

        $handles = $this->getApp()->getHandleManager()->getHandles();

        foreach($handles as $handle){
            /** @var \DaveBaker\Core\Controller\ControllerInterface $controller */
            foreach($this->getControllersForHandle($handle) as $controller){
                $controller->preDispatch();
            }

            $this->fireEvent($handle . "_predispatch");
        }

        $this->fireEvent('predispatch_after');

        return $this;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function execute()
    {
        $this->fireEvent('execute_before');
        $handles = $this->getApp()->getHandleManager()->getHandles();

        foreach($handles as $handle){
            /** @var \DaveBaker\Core\Controller\ControllerInterface $controller */
            foreach($this->getControllersForHandle($handle) as $controller){
                $controller->execute();
            }

            $this->fireEvent($handle . "_execute");
        }
        $this->fireEvent('execute_after');
        return $this;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function postDispatch()
    {
        $this->fireEvent('postdispatch_before');
        $this->_postDispatch();
        $handles = $this->getApp()->getHandleManager()->getHandles();

        foreach($handles as $handle){
            /** @var \DaveBaker\Core\Controller\ControllerInterface $controller */
            foreach($this->getControllersForHandle($handle) as $key => $controller){
                $controller->postDispatch();
            }

            $this->fireEvent($handle . "_postdispatch");
        }

        $this->fireEvent('postdispatch_after');
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
     * @param $handle
     * @param $controllerClass
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
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

        $controller = $this->getApp()->getObjectManager()->getAppObject($controllerClass);

        if(!$controller instanceof \DaveBaker\Core\Controller\ControllerInterface){
            throw new Exception("Controller is not compatible with ControllerInterface");
        }

        $this->fireEvent('register_controller', ['controller' => $controller]);

        $this->controllers[$handle][$controllerClass] = $controller;
    }

    /**
     * @param $handle
     * @return mixed
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getControllersForHandle($handle)
    {
        $controllers = [];

        if(isset($this->controllers[$handle])){
            $controllers = $this->controllers[$handle];
        }

        $context = $this->fireEvent('get_controllers_for_handle', ['controllers' => $controllers]);

        return $context->getControllers();
    }
}