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
     * @param $sql string
     * @throws Exception
     */
    public function run($sql)
    {
        $this->getDb()->query($sql);
        $this->checkError();
    }

    /**
     * @param $query
     * @throws Exception
     */
    public function getRow($query)
    {
        $this->getDb()->get_row($query);
        $this->checkError();
    }

    /**
     * @param $tableName string
     * @return string
     */
    public function getTableName($tableName)
    {
        return $this->getHelper()->getTableName($tableName);
    }

    /**
     * @param $query
     * @param $output
     * @throws Exception
     */
    public function getResults($query, $output = OBJECT)
    {
        $this->getDb()->get_results($query, $output);
        $this->checkError();
    }

    /**
     * @param $tableName string
     * @param $schema string
     * @throws Exception
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
    }

    /**
     * @return \DaveBaker\Core\Helper\Db
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