<?php

namespace DaveBaker\Core\WP\Option;

class Manager
{
    /**
     * @param $option
     * @param null $default
     * @return mixed|void
     */
    public function get($option, $default = null){
        return get_option($option, $default);
    }

    /**
     * @param $option
     * @param $value
     */
    public function set($option, $value){
        update_option($option, $value);
    }

    /**
     * @param $option
     */
    public function remove($option){
        delete_option($option);
    }
}