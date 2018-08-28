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
    public function getTableName($tableName, $useNamespace = true)
    {
        global $wpdb;
        return $wpdb->prefix .
            ($useNamespace ? $this->getApp()->getNamespace() : '') .
            $tableName;
    }
}