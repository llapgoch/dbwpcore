<?php

namespace DaveBaker\Core\App;

/**
 * Class Request
 * @package DaveBaker\Core\App
 */
class Request
{
    const GET = "get";
    const POST = "post";
    const CUSTOM = "custom";

    /** @var array */
    protected $params = [
        self::GET => [],
        self::POST => [],
        self::CUSTOM => []
    ];


    public function __construct()
    {
        $this->compileParams();
    }


    /**
     * @return $this
     *
     * Compiles a local registry of requests.
     * Undo Wordpress' Magic Quote nonsense.
     */
    protected function compileParams()
    {
        if(is_array($_GET)){
            foreach($_GET as $k => $param){
                $this->params[self::GET][$k] = stripslashes_deep($param);
            }
        }

        if(is_array($_POST)){
            foreach($_POST as $k => $param){
                $this->params[self::POST][$k] = stripslashes_deep($param);
            }
        }

        return $this;
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