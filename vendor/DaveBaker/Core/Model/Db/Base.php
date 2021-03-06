<?php

namespace DaveBaker\Core\Model\Db;
/**
 * Class Base
 * @package DaveBaker\Core\Model\Db
 */
abstract class Base
    extends \DaveBaker\Core\Object\Base
    implements \DaveBaker\Core\Model\Db\BaseInterface
{
    const MODEL_NAMESPACE = 'model';
    const DEFAULT_CREATED_AT_COLUMN = 'created_at';
    const DEFAULT_UPDATED_AT_COLUMN = 'updated_at';

    /** @var string */
    protected $tableName;
    /** @var string */
    protected $idColumn;
    /** @var array  */
    protected $schema = [];
    /** @var bool  */
    protected $autoUpdateTime = true;
    /** @var  \DaveBaker\Core\Helper\Db */
    protected $helper;
    /** @var  \DaveBaker\Core\Db\Query */
    protected $query;
    /** @var string  */
    protected $namespaceCode = "default";
    /** @var bool */
    protected $useTableNamespace = true;
    /** @var array  */
    protected $outputProcessors = [];

    // Set the table name and idColumn in an init
    protected abstract function init();


    /**
     * @return $this|\DaveBaker\Core\Object\Base
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        parent::_construct();
        $this->init();

        if(!$this->idColumn || !$this->tableName){
            throw new Exception("idColumn or tableName not set");
        }

        $this->fireEvent('create');
        return $this;
    }

    /**
     * @param $id
     * @param string $column
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function load($id, $column = '')
    {
        $this->fireEvent('load_before');

        $column = $column ? $column: $this->idColumn;

        try {
            $data = $this->getQuery()->getRow(
                $sql = $this->getDb()->prepare(
                    "SELECT * FROM {$this->getTableName()} WHERE {$column}=%s",
                    $id
                )
            );

            if($data) {
                $this->setObjectData($data);
            }

            $this->fireEvent('load');
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getData($this->idColumn);
    }

    /**
     * @param string $key
     * @return array|mixed|null
     */
    public function getOutputData($key = '')
    {
        if(isset($this->outputProcessors[$key])){
            return $this->outputProcessors[$key]->process($this->getData($key));
        }

        return $this->getData($key);
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
        $outputProcessor = clone $outputProcessor;
        $outputProcessor->setModel($this);
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
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTableName()
    {
        // Turn this off to use models with non-namespaced (typically core Wordpress tables)
        return $this->getHelper()->getTableName($this->tableName, $this->useTableNamespace);
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function delete()
    {
        $this->fireEvent('before_delete');

        if(!$this->getId()){
            return;
        }

        $this->getDb()->delete(
            $this->getTableName(),
            [$this->idColumn => $this->getId()]
        );

        $this->unsetData();

        $this->fireEvent('after_delete');

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function save()
    {
        if(!$this->getData()){
            return $this;
        }

        $this->beforeSave();
        $this->fireEvent('before_save');

        try {
            if ($this->getId()) {
                $this->updateSave();
            }else {
                $this->insertSave();
            }
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->afterSave();
        $this->fireEvent('after_save');

        return $this;
    }

    protected function beforeSave(){}
    protected function afterSave(){}

    /**
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function insertSave()
    {
        if(!($data = $this->getTableSaveData())){
            return $this;
        }

        $this->fireEvent('before_insert_save');

        if($this->getAutoUpdateTime()) {
            $currentTime = $this->getDateHelper()->utcTimestampToDb();

            if ($this->isDateTime(self::DEFAULT_CREATED_AT_COLUMN)
                && !isset($data[self::DEFAULT_CREATED_AT_COLUMN])
            ) {
                $data[self::DEFAULT_CREATED_AT_COLUMN] = $currentTime;
            }

            if ($this->isDateTime(self::DEFAULT_UPDATED_AT_COLUMN)
                && !isset($data[self::DEFAULT_UPDATED_AT_COLUMN])
            ) {
                $data[self::DEFAULT_UPDATED_AT_COLUMN] = $currentTime;
            }
        }

        $res = $this->getQuery()->insert(
            $this->tableName,
            $data
        );

        if(!$res){
            throw new Exception('An error occurred saving to the database');
        }

        $this->setData($this->idColumn, $this->getDb()->insert_id);

        $this->fireEvent('after_insert_save');

        return $this;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function updateSave()
    {
        if(!($data = $this->getTableSaveData())){
            return $this;
        }

        $this->fireEvent('before_update_save');

        // Always update updated_at columns on save
        if($this->isDateTime(self::DEFAULT_UPDATED_AT_COLUMN) && $this->getAutoUpdateTime()){
            $data[self::DEFAULT_UPDATED_AT_COLUMN] = $this->getDateHelper()->utcTimestampToDb();
        }

        $this->getQuery()->update(
            $this->tableName,
            $data,
            [$this->idColumn => $this->getId()]
        );

        $this->fireEvent('after_update_save');

        return $this;
    }

    /**
     * @param $update
     * @return $this
     *
     * Sets whether the object automatically populates created_at and updated_at
     */
    public function setAutoUpdateTime($update)
    {
        $this->autoUpdateTime = $update;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoUpdateTime()
    {
        return $this->autoUpdateTime;
    }

    protected function isColumnType($column, $types = [])
    {
        $schema = $this->getSchema();

        if(!isset($schema[$column])){
            return false;
        }

        if(!is_array($types)){
            $types = [$types];
        }

        return in_array($schema[$column]['type'], $types);
    }

    /**
     * @param string $column
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function isDateTime($column)
    {
        return $this->isColumnType($column, 'datetime');
    }

    /**
     * @param string $column
     * @return bool
     */
    protected function isNumeric($column)
    {
        return $this->isColumnType($column, ['int', 'decimal', 'float', 'double']);
    }

    /**
     * @return object
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getDateHelper()
    {
        return $this->getApp()->getHelper('Date');
    }

    /**
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTableSaveData()
    {
        $dataItems = array_intersect_key($this->getData(), $this->getSchema());

        foreach($dataItems as $k => $value){
            if($this->isNumeric($k) && is_numeric($value) == false){
                $dataItems[$k] = null;
            }
        }

        return $dataItems;
    }

    /**
     * @param \stdClass $data
     * @return $this
     */
    public function setObjectData(\stdClass $data)
    {
        foreach(get_object_vars($data) as $k=>$d){
            $this->setData($k, $d);
        }

        return $this;
    }

    /**
     * @param string $event
     * @return string
     */
    public function getNamespacedEvent($event)
    {
        return self::MODEL_NAMESPACE .
            "_" . $this->tableName .
            "_" . $event;
    }

    /**
     * @param string $optionCode
     * @return string
     */
    public function getNamespacedOption($optionCode)
    {
        return $this->getApp()->getNamespace() .
            "_" . self::MODEL_NAMESPACE .
            "_" .$this->tableName .
            "_" . $optionCode;
    }

    /**
     * @param bool $force
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     *
     * TODO: Add caching
     */
    public function getSchema($force = false)
    {
        if(!$this->schema || $force) {
            $rows = $this->getDb()->get_results(
                "SHOW COLUMNS FROM {$this->getTableName()}"
            );

            foreach ($rows as $row) {
                $type = explode("(", $row->Type);
                $this->schema[$row->Field] = [
                    "type" => $type[0]
                ];
            }
        }

        return $this->schema;
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