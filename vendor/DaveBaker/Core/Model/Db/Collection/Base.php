<?php

namespace DaveBaker\Core\Model\Db\Collection;
/**
 * Class Base
 * @package DaveBaker\Core\Model\Db\Collection
 */
abstract class Base extends \DaveBaker\Core\Base
{
    const COLLECTION_NAMESPACE = 'collection';
    /** @var string */
    protected $dbClass;
    /** @var  \DaveBaker\Core\Model\Db\Base */
    protected $baseObject;
    /** @var array */
    protected $items = [];
    /** @var  \Zend_Db_Select */
    protected $select;
    /** @var  \Zend_Db_Adapter_Pdo_Mysql */
    protected $adapter;
    /** @var  \DaveBaker\Core\Helper\Db */
    protected $helper;
    /** @var  \DaveBaker\Core\Db\Query */
    protected $query;
    
    public function _construct()
    {
        $this->init();

        if(!$this->dbClass){
            throw new Exception('dbClass not set');
        }

        try{
            $this->baseObject = new $this->dbClass();
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->fireEvent('create');
    }

    protected abstract function init();

    /**
     * @return $this
     */
    protected function initSelect()
    {
        if(!$this->select) {
            $this->select = new \Zend_Db_Select($this->getAdapter());
            $this->select->from($this->baseObject->getTableName(), "*");
        }

        return $this;
    }



    /**
     * @return \Zend_Db_Select
     */
    public function getSelect(){
        $this->initSelect();
        return $this->select;
    }

    /**
     * @param \Zend_Db_Adapter_Pdo_Mysql|null $adapter
     * @return $this
     */
    public function setAdapter(\Zend_Db_Adapter_Pdo_Mysql $adapter = null)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @return \Zend_Db_Adapter_Pdo_Mysql
     */
    public function getAdapter()
    {
        if(!$this->adapter){
            return \DaveBaker\Core\Model\Db\Registry::getGlobalZendAdapter();
        }

        return $this->adapter;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->fireEvent('before_reset');
        $this->initSelect();
        $this->select->reset();
        $this->resetItems();
        $this->fireEvent('after_reset');

        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param string $event
     * @return string
     */
    public function getNamespacedEvent($event)
    {
        return self::COLLECTION_NAMESPACE .
            "_" . $this->baseObject->getTableName() .
            "_" . $event;
    }

    /**
     * @param string $optionCode
     * @return string
     */
    public function getNamespacedOption($optionCode)
    {
        return $this->getApp()->getNamespace() .
            "_" . self::COLLECTION_NAMESPACE .
            "_" . $this->baseObject->getTableName() .
            "_" . $optionCode;
    }

    /**
     * @return array
     */
    public function load()
    {
        $this->fireEvent('before_load');
        $this->initSelect();

        $results = $this->getQuery()->getResults($this->select->assemble());

        foreach($results as $result){
            /** @var \DaveBaker\Core\Model\Db\Base $item */
            $item = new $this->dbClass;
            $item->setObjectData($result);
            $this->items[] = $item;
        }

        $this->fireEvent('after_load');

        return $this->items;
    }

    /**
     * @return $this
     */
    protected function resetItems()
    {
        $this->items = [];
        return $this;
    }

    /**
     * @return \DaveBaker\Core\Helper\Db
     */
    protected function getHelper()
    {
        if(!$this->helper) {
            $this->helper = $this->getApp()->getHelper('Db');
        }

        return $this->helper;
    }

    /**
     * @return \DaveBaker\Core\Db\Query
     */
    protected function getQuery()
    {
        if(!$this->query){
            $this->query = $this->createAppObject('\DaveBaker\Db\Query');
        }

        return $this->query;
    }

}