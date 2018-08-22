<?php

namespace DaveBaker\Core\WP\Model\Db;

abstract class Base
    extends \DaveBaker\Core\WP\Object\Base
    implements \DaveBaker\Core\WP\Model\Db\BaseInterface
{
    const MODEL_NAMESPACE = 'model';

    /** @var  \wpdb */
    protected $wpdb;
    protected $tableName;
    protected $idColumn;
    protected $schema = [];

    protected $namespaceCode = "default";

    // Set the table name and idColumn in an init
    protected abstract function init();

    /**
     * @param $id
     * @param string $column
     * @throws Exception
     */
    public function load($id, $column = '')
    {
        $column = $column ? $column: $this->idColumn;

        try {
            $data = $this->wpdb->get_row(
                $this->wpdb->prepare(
                    "SELECT * FROM {$this->getTableName()} WHERE {$column}=%s",
                    $id
                )
            );

            if($data) {
                $this->setObjectData($data);
            }
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
        return $this->wpdb->base_prefix . $this->tableName;
    }

    /**
     * @return $this|void
     */
    public function delete()
    {
        if(!$this->getId()){
            return;
        }

        $this->wpdb->delete(
            $this->getTableName(),
            [$this->idColumn => $this->getId()]
        );

        $this->unsetData();

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

        try {
            if ($this->getId()) {
                return $this->updateSave();
            }

            return $this->insertSave();

        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function insertSave()
    {
        $res = $this->wpdb->insert(
            $this->getTableName(),
            $this->getTableSaveData()
        );

        if(!$res){
            throw new Exception('An error occurred saving to the database');
        }

        $this->setData($this->idColumn, $this->wpdb->insert_id);

        return $this;
    }

    /**
     * @return $this
     */
    protected function updateSave()
    {
        $this->wpdb->update(
            $this->getTableName(),
            $this->getTableSaveData(),
            [$this->idColumn => $this->getId()]
        );

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
        "_" . $this->namespaceCode .
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
        "_" .$this->namespaceCode .
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

        $rows = $this->wpdb->get_results(
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