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
        $this->tableName = 'file_upload';
        $this->idColumn = 'id';
    }
}