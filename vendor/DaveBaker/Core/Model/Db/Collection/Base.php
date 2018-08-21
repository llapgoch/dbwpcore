<?php

namespace DaveBaker\Core\Model\Db\Collection;

abstract class Base
{
    /**
     * @var \wpdb
     */
    protected $wpdb;
    protected $dbClass;
    /** @var  \DaveBaker\Core\Model\Db\Base */
    protected $baseObject;
    protected $items = [];
    protected $select;
    protected $adapter;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->init();

        if(!$this->dbClass){
            throw new Exception('dbClass not set');
        }

        try{
            $this->baseObject = new $this->dbClass();
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
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
        $this->initSelect();
        $this->select->reset();
        $this->resetItems();

        return $this;
    }

    /**
     * @return $this
     */
    public function resetItems()
    {
        $this->items = [];
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
     * @return array
     */
    public function load()
    {
        $this->initSelect();

        $results = $this->wpdb->get_results($this->select->assemble());

        foreach($results as $result){
            /** @var \DaveBaker\Core\Model\Db\Base $item */
            $item = new $this->dbClass;
            $item->setObjectData($result);
            $this->items[] = $item;
        }

        return $this->items;
    }

}