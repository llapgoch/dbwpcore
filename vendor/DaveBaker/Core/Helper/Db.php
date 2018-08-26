<?php

namespace DaveBaker\Core\Helper;
/**
 * Class Db
 * @package DaveBaker\Core\Helper
 */
class Db extends Base
{
    /**
     * @param $tableName string
     * @return string
     */
    public function getTableName($tableName)
    {
        global $wpdb;
        return $wpdb->prefix . $this->getApp()->getNamespace() . $tableName;
    }
}