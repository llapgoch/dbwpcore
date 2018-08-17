<?php

namespace DaveBaker\Core\Model\Db;

abstract class Base extends \DaveBaker\Core\Model\Base
{
    /** @var  \wpdb */
    protected $wpdb;
    protected $tableName;
    protected $idColumn;
    protected $schema = [];

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->init();

        if(!$this->idColumn |! $this->tableName){
            throw new Exception('ID column or table not set');
        }

        $this->loadSchema();
    }

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
     * @return string
     */
    public function getTableName()
    {
        return $this->wpdb->base_prefix . $this->tableName;
    }

    public function save()
    {
        try {
            if ($this->getData($this->idColumn)) {
                return $this->updateSave();
            }

            return $this->insertSave();

        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    protected function insertSave()
    {
        $this->setHorse("gorse");


        $res = $this->wpdb->insert(
            $this->getTableName(),
            $this->getTableSaveData()
        );

        if(!$res){
            throw new Exception('An error occurred saving to the database');
        }

        $this->setData($this->idColumn, $this->wpdb->insert_id);
    }

    protected function updateSave()
    {
        $this->wpdb->update(
            $this->getTableName(),
            $this->getTableSaveData(),
            [$this->idColumn => $this->getData($this->idColumn)]
        );
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
     */
    protected function setObjectData(\stdClass $data)
    {
        foreach(get_object_vars($data) as $k=>$d){
            $this->setData($k, $d);
        }
    }

    /**
     * @param bool $force
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

    }
}