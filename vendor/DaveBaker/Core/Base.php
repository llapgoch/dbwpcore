<?php

namespace DaveBaker\Core;
/**
 * Class Base
 * @package DaveBaker\Core
 */
abstract class Base
{
    /** @var \DaveBaker\Core\App  */
    protected $app;
    /** @var \DaveBaker\Core\Option\Manager */
    protected $optionManager;
    /** @var  \wpdb */
    protected $db;
    /** @var string  */
    protected $namespaceCode = 'default_';

    public function __construct(
        \DaveBaker\Core\App $app
    ) {
        $this->app = $app;
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
     * @throws Object\Exception
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
     * @param $event
     * @param array $params
     * @return Event\Context
     * @throws Event\Exception
     * @throws Object\Exception
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
     * @param $event
     * @param bool $callback
     * @return $this
     * @throws Object\Exception
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
     * @param $optionCode
     * @param $value
     * @return $this
     * @throws Object\Exception
     */
    public function setOption($optionCode, $value)
    {
        $this->getOptionManager()->set($this->getNamespacedOption($optionCode), $value);
        return $this;
    }

    /**
     * @param $optionCode
     * @return mixed
     * @throws Object\Exception
     */
    public function getOption($optionCode)
    {
        return $this->getOptionManager()->get($this->getNamespacedOption($optionCode));
    }

    /**
     * @param $event string
     * @return string
     *
     */
    public function getNamespacedEvent($event)
    {
        return $this->namespaceCode . "_" . $event;
    }

    /**
     * @param $className
     * @param $args
     * @return object
     * @throws Object\Exception
     */
    public function createObject($className, $args = [])
    {
        return $this->getApp()->getObjectManager()->get($className, $args);
    }

    /**
     * @param $identifier
     * @param array $args
     * @return mixed
     * @throws Object\Exception
     *
     * Returns an object which typically extends Core/Base and automatically
     * passes in Core/App as the first parameter
     */
    public function createAppObject($identifier, $args = [])
    {
        return $this->getApp()->getObjectManager()->getAppObject($identifier, $args);
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
     * @return Option\Manager|object
     * @throws Object\Exception
     */
    protected function getOptionManager()
    {
        return $this->getApp()->getOptionManager();
    }

    /**
     * @return Event\Manager|object
     * @throws Object\Exception
     */
    protected function getEventManager()
    {
        return $this->getApp()->getEventManager();
    }

    /**
     * @return App\Request|object
     * @throws Object\Exception
     */
    public function getRequest()
    {
        return $this->getApp()->getRequest();
    }

    /**
     * @return \wpdb
     */
    protected function getDb()
    {
        if(!$this->db){
            global $wpdb;
            $this->db = $wpdb;
        }

        return $this->db;
    }

    /**
     * @return \DaveBaker\Core\Helper\Date
     * @throws Object\Exception
     */
    protected function getDateHelper()
    {
        return $this->getApp()->getHelper('Date');
    }

    /**
     * @return \DaveBaker\Core\Helper\Util
     * @throws Object\Exception
     */
    protected function getUtilHelper()
    {
        return $this->getApp()->getHelper('Util');
    }

    /**
     * @return \DaveBaker\Core\Helper\Db
     * @throws Object\Exception
     */
    protected function getDbHelper()
    {
        return $this->getApp()->getHelper('Db');
    }

    /**
     * @return \DaveBaker\Core\Helper\Url
     * @throws Object\Exception
     */
    protected function getUrlHelper()
    {
        return $this->getApp()->getHelper('Url');
    }

    /**
     * @return \DaveBaker\Core\Helper\User
     * @throws Object\Exception
     */
    protected function getUserHelper()
    {
        return $this->getApp()->getHelper('User');
    }

    /**
     * @return \DaveBaker\Core\Helper\Locale
     * @throws Object\Exception
     */
    protected function getLocaleHelper()
    {
        return $this->getApp()->getHelper('Locale');
    }

    /**
     * @return \DaveBaker\Core\Helper\Upload
     * @throws Object\Exception
     */
    protected function getUploadHelper()
    {
        return $this->getApp()->getHelper('Upload');
    }
}