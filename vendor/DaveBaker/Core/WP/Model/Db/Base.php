<?php

namespace DaveBaker\Core\WP\Model\Db;

abstract class Base
    extends \DaveBaker\Core\WP\Object\Base
    implements \DaveBaker\Core\WP\Model\Db\BaseInterface
{
    const MODEL_NAMESPACE = 'model';

    protected $tableName;
    protected $idColumn;
    protected $schema = [];

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
     */
    public function load($id, $column = '')
    {
        $this->fireEvent('before_load');

        $column = $column ? $column: $this->idColumn;

        try {
            $data = $this->getDb()->get_row(
                $sql = $this->getDb()->prepare(
                    "SELECT * FROM {$this->getTableName()} WHERE {$column}=%s",
                    $id
                )
            );

            if($data) {
                $this->setObjectData($data);
            }

            $this->fireEvent('after_load');
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
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
        return $this->getDb()->base_prefix . $this->tableName;
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
        $this->fireEvent('before_insert_save');

        $res = $this->getDb()->insert(
            $this->getTableName(),
            $this->getTableSaveData()
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
        $this->fireEvent('before_update_save');

        $this->getDb()->update(
            $this->getTableName(),
            $this->getTableSaveData(),
            [$this->idColumn => $this->getId()]
        );

        $this->fireEvent('after_update_save');

        return $this;
    }

    /**
     * @return array
     */
    protected function getTableSaveData()
    {
        return array_intersect_key($this->getData(), $this->schema);
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
    protected function loadSchema($force = false)
    {
        if($this->schema && !$force){
            return;
        }

        $rows = $this->getDb()->get_results(
            "SHOW COLUMNS FROM {$this->getTableName()}"
        );

        foreach($rows as $row){
            $type = explode("(", $row->Type);
            $this->schema[$row->Field] = [
                "type" => $type[0]
            ];
        }

        return $this;
    }
}