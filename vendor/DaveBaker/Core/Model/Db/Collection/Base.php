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
    /** @var array */
    protected $outputProcessors = [];
    
    public function _construct()
    {
        $this->init();

        if(!$this->dbClass){
            throw new Exception('dbClass not set');
        }

        try{
            $this->baseObject = $this->createAppObject($this->dbClass);
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->fireEvent('create');
    }

    protected abstract function init();

    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
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
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
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
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getAdapter()
    {
        if(!$this->adapter){
            return \DaveBaker\Core\Model\Db\Registry::getGlobalZendAdapter();
        }

        return $this->adapter;
    }

    /**
     * @param $spec
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function order($spec)
    {
        $specReplaced = $this->replaceTablesIn($spec);

        if($spec instanceof \Zend_Db_Expr){
            $specReplaced = new \Zend_Db_Expr($specReplaced);
        }

        $this->getSelect()->order($specReplaced);
        return $this;
    }

    /**
     * @param $cond
     * @param null $value
     * @param null $type
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function where($cond, $value = null, $type = null)
    {
        $cond = $this->replaceTablesIn($cond);
        $this->getSelect()->where($cond, $value, $type);
        return $this;
    }


    /**
     * @param $name
     * @param $cond
     * @param string $cols
     * @param null $schema
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function joinLeft($name, $cond, $cols = \Zend_Db_Select::SQL_WILDCARD, $schema = null)
    {
        $name = $this->replaceTablesIn($name);
        $cond = $this->replaceTablesIn($cond);

        $this->getSelect()->joinLeft($name, $cond, $cols, $schema);

        return $this;
    }

    /**
     * @param $name
     * @param $cond
     * @param string $cols
     * @param null $schema
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function joinRight($name, $cond, $cols = \Zend_Db_Select::SQL_WILDCARD, $schema = null)
    {
        $name = $this->replaceTablesIn($name);
        $cond = $this->replaceTablesIn($cond);

        $this->getSelect()->joinRight($name, $cond, $cols, $schema);

        return $this;
    }

    /**
     * @param $name
     * @param $cond
     * @param $cols
     * @param null $schema
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function join($name, $cond, $cols = self::SQL_WILDCARD, $schema = null)
    {
        $name = $this->replaceTablesIn($name);
        $cond = $this->replaceTablesIn($cond);

        $this->getSelect()->joinInner($name, $cond, $cols, $schema);

        return $this;
    }

    /**
     * @param $string
     * @return null|string|string[]
     */
    public function replaceTablesIn($strings)
    {
        $returnString = false;

        if(!is_array($strings)){
            $strings = [$strings];
            $returnString = true;
        }

        foreach($strings as $k => $string) {
            $string = preg_replace_callback(
                "/{{([^}]+)}}/",
                function ($matches) {
                    return $this->getTableName($matches[1]);
                },
                $string
            );

            $strings[$k] = $string;
        }

        if($returnString){
            return $strings[0];
        }

        return $strings;
    }

    /**
     * @param array $processors
     * @return $this
     */
    public function addOutputProcessors($processors)
    {
        foreach($processors as $k => $processor){
            $this->registerOutputProcessor($k, $processor);
        }

        $this->setOutputProcessorsOnItems();
        return $this;
    }

    /**
     * @param string $dataName
     * @param \DaveBaker\Core\Helper\OutputProcessor\OutputProcessorInterface $outputProcessor
     * @return $this
     */
    protected final function registerOutputProcessor(
        $dataName,
        \DaveBaker\Core\Helper\OutputProcessor\OutputProcessorInterface $outputProcessor
    ) {
        $this->outputProcessors[$dataName] = $outputProcessor;
        return $this;
    }

    /**
     * @return array
     */
    public function getOutputProcessors()
    {
        return $this->outputProcessors;
    }

    /**
     * @param $tableName
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTableName($tableName)
    {
        return $this->getQuery()->getTableName($tableName);
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
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
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getItems()
    {
        $this->load();
        return $this->items;
    }

    /**
     * @param string $event
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
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
     * @throws \DaveBaker\Core\Object\Exception
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
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function load()
    {
        if($this->items){
            return $this->items;
        }

        $this->fireEvent('before_load');
        $this->initSelect();

        $results = $this->getQuery()->getResults($this->select->assemble());

        foreach($results as $result){
            /** @var \DaveBaker\Core\Model\Db\Base $item */
            $item = $this->createAppObject($this->dbClass);
            $item->setObjectData($result);

            $this->items[] = $item;
        }

        $this->setOutputProcessorsOnItems();

        $this->fireEvent('after_load');

        return $this->items;
    }

    /**
     * @return mixed|null
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function firstItem()
    {
        $items = $this->load();

        if(count($items)){
            return $items[0];
        }

        return null;
    }

    /**
     * @return array
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getAllIds()
    {
        $ids = [];
        foreach($this->load() as $item){
            $ids[] = $item->getId();
        }

        return $ids;
    }

    /**
     * @param $valueKey
     * @return array
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     *
     * Returns all values from models with a particular key
     */
    public function getAllValuesFor($valueKey)
    {
        $values = [];

        foreach($this->load() as $item){
            if($data = $item->getData($valueKey)) {
                $values[] = $data;
            }
        }

        return $values;
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
     * @return $this
     */
    protected function setOutputProcessorsOnItems()
    {
        foreach($this->items as $item){
            $item->addOutputProcessors($this->getOutputProcessors());
        }

        return $this;
    }

    /**
     * @return \DaveBaker\Core\Helper\Db|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getHelper()
    {
        if(!$this->helper) {
            $this->helper = $this->getApp()->getHelper('Db');
        }

        return $this->helper;
    }

    /**
     * @return \DaveBaker\Core\Db\Query|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuery()
    {
        if(!$this->query){
            $this->query = $this->createAppObject('\DaveBaker\Core\Db\Query');
        }

        return $this->query;
    }
}