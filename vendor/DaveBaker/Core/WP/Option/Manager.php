<?php

namespace DaveBaker\Core\WP\Option;

class Manager
{
    protected $namespace;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param $option
     * @return string
     */
    public function getOptionKey($option){
        return $this->getNamespace() . $option;
    }

    /**
     * @param $option
     * @param null $default
     * @return mixed|void
     */
    public function getOption($option, $default = null){
        return get_option($this->getNamespace() . $option, $default);
    }

    /**
     * @param $option
     * @param $value
     */
    public function updateOption($option, $value){
        update_option($this->getNamespace() . $option, $value);
    }

    /**
     * @param $option
     */
    public function removeOption($option){
        delete_option($this->getNamespace() . $option);
    }

    public function getNamespace()
    {
        return $this->namespace;
    }


}