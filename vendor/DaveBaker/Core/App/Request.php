<?php

namespace DaveBaker\Core\App;

class Request
{
    const GET = "get";
    const POST = "post";
    const CUSTOM = "custom";

    protected $params = [
        self::GET => [],
        self::POST => [],
        self::CUSTOM => []
    ];

    public function __construct()
    {
        // Parse parameters
        if(is_array($_GET)){
            foreach($_GET as $k => $param){
                $this->params[self::GET][$k] = $param;
            }
        }

        if(is_array($_POST)){
            foreach($_POST as $k => $param){
                $this->params[self::POST][$k] = $param;
            }
        }
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->params[self::CUSTOM][$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return null|string
     */
    public function getParam($key, $default = null)
    {
        if($param = $this->getCustomParam($key, false)){
            return $param;
        }

        if($param = $this->getPostParam($key, false)){
            return $param;
        }

        if($param = $this->getGetParam($key, false)){
            return $param;
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getPostParams()
    {
        return $this->params[self::POST];
    }

    /**
     * @param $key
     * @param string $default
     * @return string
     */
    public function getPostParam($key, $default = '')
    {
        if(isset($this->params[self::POST][$key])){
            return $this->params[self::POST][$key];
        }

        return $default;
    }

    /**
     * @param $key
     * @param string $default
     * @return string
     */
    public function getGetParam($key, $default = '')
    {
        if(isset($this->params[self::GET][$key])){
            return $this->params[self::GET][$key];
        }

        return $default;
    }

    /**
     * @param $key
     * @param string $default
     * @return string
     */
    public function getCustomParam($key, $default = '')
    {
        if(isset($this->params[self::CUSTOM][$key])){
            return $this->params[self::CUSTOM][$key];
        }

        return $default;
    }
}