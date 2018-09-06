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

    /**
     * @param $role
     * @param $displayName
     * @param $capabilities
     */
    public function addRole($role, $displayName, $capabilities = [], $namespacedRole = true, $namespacedCap = true)
    {
        global $wp_roles;

        if($namespacedRole){
            $role = $this->getNamespacedOption($role);
        }

        if(!is_array($capabilities)){
            $capabilities = [$capabilities];
        }

        if($namespacedCap){
            foreach($capabilities as $k => $capability){
                $capabilities[$k] = $this->getNamespacedOption($capability);
            }
        }

        $wp_roles->add_role($role, $displayName, $capabilities);
    }

    /**
     * @param $role
     * @param $cap
     * @param bool $grant
     */
    public function addCapability($role, $cap, $grant = true, $namespacedRole = true, $namespacedCap = true)
    {
        global $wp_roles;

        if($namespacedCap){
            $cap = $this->getNamespacedOption($cap);
        }

        if($namespacedRole){
            $role = $this->getNamespacedOption($role);
        }

        $wp_roles->add_cap($role, $cap, $grant);
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
                $capability[$k] = $this->getNamespacedOption($cap);

                if(!current_user_can($cap)){
                    return false;
                }
            }
        }

        foreach($capability as $cap){
            if(!current_user_can($cap)){
                return false;
            }
        }

        return true;
    }




}