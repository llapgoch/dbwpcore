<?php

namespace DaveBaker\Core\Db;
/**
 * Class Query
 * @package DaveBaker\Core\Db
 */
class Query extends \DaveBaker\Core\Base
{
    const TABLE_NAME_REPLACER = '{{tableName}}';

    /** @var \DaveBaker\Core\Helper\Db */
    protected $helper;

    /**
     * @param $sql
     * @return false|int
     * @throws Exception
     */
    public function run($sql)
    {
        $results = $this->getDb()->query($sql);
        $this->checkError();

        return $results;
    }

    /**
     * @param $query
     * @return array|null|object|void
     * @throws Exception
     */
    public function getRow($query)
    {
        $row = $this->getDb()->get_row($query);
        $this->checkError();

        return $row;
    }

    /**
     * @param $tableName
     * @param $data
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function insert($tableName, $data)
    {
        $returnVal = $this->getDb()->insert(
            $this->getTableName($tableName),
            $data
        );

        if(!$returnVal){
            throw new Exception('An error occurred inserting');
        }

        $this->checkError();

        return $this;
    }

    /**
     * @param string $tableName
     * @param array $data
     * @param array $where
     * @param null $format
     * @param null $whereFormat
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function update($tableName, $data, $where, $format = null, $whereFormat = null)
    {
        $returnVal = $this->getDb()->update(
            $this->getTableName($tableName),
            $data,
            $where,
            $format,
            $whereFormat
        );

        if(!$returnVal){
            throw new Exception('An error occurred updating');
        }

        $this->checkError();
        return $this;
    }

    /**
     * @param $tableName
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTableName($tableName, $useNamespace = true)
    {
        return $this->getHelper()->getTableName($tableName, $useNamespace);
    }

    /**
     * @param $query
     * @param string $output
     * @return array|null|object
     * @throws Exception
     */
    public function getResults($query, $output = OBJECT)
    {
        $results = $this->getDb()->get_results($query, $output);
        $this->checkError();

        return $results;
    }

    /**
     * @param string $tableName
     * @param string $schema
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function deltaTable($tableName, $schema)
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        if(strpos($schema, self::TABLE_NAME_REPLACER) === false){
            throw new Exception('deltaTable requires a {{tableName}} definition to be present in the table schema');
        }

        $schema = str_replace(
            self::TABLE_NAME_REPLACER,
            $this->getTableName($tableName),
            $schema
        );

        dbDelta($schema);
        $this->checkError();

        return $this;
    }

    /**
     * @return object
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getHelper()
    {
        if(!$this->helper){
            $this->helper = $this->createAppObject('\DaveBaker\Core\Helper\Db');
        }

        return $this->helper;
    }

    /**
     * @return $this
     * @throws Exception
     *
     * Call this after every query, provides exceptions which halt execution rather
     * than Wordpress' method of displaying errors and continuing code execution
     */
    protected function checkError()
    {
        if($error = $this->getDb()->last_error){
            throw new Exception($error);
        }

        return $this;
    }
}