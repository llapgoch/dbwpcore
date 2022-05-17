<?php

namespace DaveBaker\Core\App;

/**
 * Class Request
 * @package DaveBaker\Core\App
 */
class Registry
{
    /** @var array  */
    protected $data = [];

    /**
     * @param $key
     * @param $value
     */
    public function register($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if(isset($this->data[$key])){
            return $this->data[$key];
        }

        return $default;
    }

}