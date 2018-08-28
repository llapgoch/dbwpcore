<?php

namespace DaveBaker\Core\Helper;
/**
 * Class User
 * @package DaveBaker\Core\Helper
 */
class User extends Base
{
    public function getUserCollection()
    {
        return $this->createAppObject('\DaveBaker\Core\Model\Db\Core\User\Collection');
    }
}