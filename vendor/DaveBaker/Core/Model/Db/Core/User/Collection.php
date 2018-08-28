<?php

namespace DaveBaker\Core\Model\Db\Core\User;
/**
 * Class User
 * @package DaveBaker\Core\Model\Db
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    protected function init()
    {
        $this->dbClass = '\DaveBaker\Core\Model\Db\Core\User';
    }
}