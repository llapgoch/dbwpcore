<?php

namespace DaveBaker\Core\Helper;
/**
 * Class User
 * @package DaveBaker\Core\Helper
 */
class User extends Base
{
    /**
     * @return object
     * @throws \DaveBaker\Core\Object\Exception
     * TODO: Querying using roles, meta
     */
    public function getUserCollection()
    {
        return $this->createAppObject('\DaveBaker\Core\Model\Db\Core\User\Collection');
    }

    /**
     * @param $userId
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUser($userId)
    {
        return $this->createAppObject('\DaveBaker\Core\Model\Db\Core\User')->load($userId);
    }

    /**
     * @return int
     */
    public function getCurrentUserId()
    {
        return get_current_user_id();
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool) $this->getCurrentUserId();
    }
}