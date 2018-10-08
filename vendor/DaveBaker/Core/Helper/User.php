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
     */
    public function getUserCollection()
    {
        /** @var \DaveBaker\Core\Model\Db\Core\User\Collection $users */
        return $this->createAppObject('\DaveBaker\Core\Model\Db\Core\User\Collection');
    }

    /**
     * @param $role
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUsersForRole($role)
    {
        $users = get_users([
            'role__in' => $this->getNamespacedOption($role)
        ]);

        $userIds = [];

        if(!count($users)){
            return [];
        }

        /** @var \WP_User $user */
        foreach($users as $user){
            $userIds[] = $user->ID;
        }

        return $this->getUserCollection()->where('ID IN(?)', $userIds);
    }

    /**
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUserTableName()
    {
        return $userTable = $this->getDbHelper()->getTableName('users', false);
    }

    /**
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUserMetaTableName()
    {
        return $userTable = $this->getDbHelper()->getTableName('usermeta', false);
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
     * @param bool $wp
     * @return mixed|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getCurrentUser($wp = false)
    {
        if(!$this->getCurrentUserId()){
            return false;
        }

        if($wp){
            return get_current_user();
        }

        return $this->getUser($this->getCurrentUserId());
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool) $this->getCurrentUserId();
    }

    /**
     * @param $role
     * @param $displayName
     * @param $capabilities
     * @return $this
     */
    public function addRole(
        $role,
        $displayName,
        $capabilities = [],
        $namespacedRole = true,
        $namespacedCap = true
    ) {
        global $wp_roles;

        if($namespacedRole){
            $role = $this->getNamespacedOption($role);
        }

        if(!is_array($capabilities)){
            $capabilities = [$capabilities];
        }

        $wp_roles->add_role($role, $displayName);

        foreach($capabilities as $capability){
            $this->addCapability($role, $capability, true, false);
        }

        return $this;
    }

    /**
     * @param $role
     * @param $cap
     * @param bool $grant
     * @return $this
     */
    public function addCapability(
        $role,
        $cap,
        $grant = true,
        $namespacedRole = true,
        $namespacedCap = true
    ) {
        global $wp_roles;

        if($namespacedCap){
            $cap = $this->getNamespacedOption($cap);
        }

        if($namespacedRole){
            $role = $this->getNamespacedOption($role);
        }

        $wp_roles->add_cap($role, $cap, $grant);

        return $this;
    }

    /**
     * @param $capability
     * @return bool
     */
    public function hasCapability($capability, $namespacedCap = true)
    {
        if(!is_array($capability)){
            $capability = [$capability];
        }

        if($namespacedCap){
            foreach($capability as $k => $cap) {
                $cap = $this->getNamespacedOption($cap);

                if(current_user_can($cap)){
                    return true;
                }
            }
        }

        foreach($capability as $cap){
            if(current_user_can($cap)){
                return true;
            }
        }

        return false;
    }




}