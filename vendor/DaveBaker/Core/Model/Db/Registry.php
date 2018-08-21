<?php

namespace DaveBaker\Core\Model\Db;

class Registry
{
    static protected $globalZendAdapter;

    /**
     * @return \Zend_Db_Adapter_Pdo_Mysql
     */
    public static function getGlobalZendAdapter()
    {
        if(!self::$globalZendAdapter) {
            self::$globalZendAdapter = new \Zend_Db_Adapter_Pdo_Mysql(
                new \Zend_Config([
                    'driver' => 'pdo_mysql',
                    'dbname' => DB_NAME,
                    'username' => DB_USER,
                    'password' => DB_PASSWORD
                ])
            );
        }

        return self::$globalZendAdapter;
    }
}