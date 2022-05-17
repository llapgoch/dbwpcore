<?php

namespace DaveBaker\Core\Model\Db\Core;
/**
 * Class User
 * @package DaveBaker\Core\Model\Db
 */
class User extends \DaveBaker\Core\Model\Db\Base
{
    protected function init()
    {
        $this->tableName = 'users';
        $this->idColumn = 'ID';
        $this->useTableNamespace = false;
    }
}