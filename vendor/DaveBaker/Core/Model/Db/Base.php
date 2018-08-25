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

    protected $tableName;
    protected $idColumn;
    protected $schema = [];
    protected $autoUpdateTime = true;
    /** @var  \DaveBaker\Core\Helper\Db */
    protected $helper;
    /** @var  \DaveBaker\Core\Db\Query */
    protected $query;

    protected $namespaceCode = "default";

    // Set the table name and idColumn in an init
    protected abstract function init();


    /**
     * @return $this
     * @throws Exception
     *
     * Set up the database details in init()
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
     * @throws Exception
     * @return $this
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
     * @return string
     */
    public function getTableName()
    {
        return $this->getHelper()->getTableName($this->tableName);
    }



    /**
     * @return $this|void
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
     * @return $this|void
     * @throws Exception
     */
    public function save()
    {
        if(!$this->getData()){
            return;
        }

        $this->fireEvent('before_save');

        try {
            if ($this->getId()) {
                return $this->updateSave();
            }

            return $this->insertSave();

        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->fireEvent('after_save');

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
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

        $res = $this->getDb()->insert(
            $this->getTableName(),
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

        $this->getDb()->update(
            $this->getTableName(),
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

    /**
     * @param $column string
     * @return bool
     */
    protected function isDateTime($column)
    {
        $schema = $this->getSchema();

        if(!isset($schema[$column])){
            return false;
        }

        return $schema[$column]['type'] == 'datetime';
    }

    /**
     * @return \DaveBaker\Core\Helper\Date
     */
    protected function getDateHelper()
    {
        return $this->getApp()->getHelper('Date');
    }

    /**
     * @return array
     */
    protected function getTableSaveData()
    {
        return array_intersect_key($this->getData(), $this->getSchema());
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
     * @return $this
     *
     * TODO: Add caching to this
     */
    protected function getSchema($force = false)
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