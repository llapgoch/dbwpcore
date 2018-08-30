<?php

namespace DaveBaker\Core\App;

/**
 * Class Request
 * @package DaveBaker\Core\App
 */
class Request extends \DaveBaker\Core\Base
{
    const GET = "get";
    const POST = "post";
    const CUSTOM = "custom";

    const RETURN_URL_PARAM = '_ret';

    /** @var array */
    protected $params = [
        self::GET => [],
        self::POST => [],
        self::CUSTOM => []
    ];


    protected function _construct()
    {
        $this->compileParams();
    }


    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Compiles a local registry of requests.
     * Undo Wordpress' Magic Quote nonsense.
     * Sets the return url in the session for later local use
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

        if($returnUrl = base64_decode($this->getParam(self::RETURN_URL_PARAM))){
            $this->getApp()->getGeneralSession()->set(self::RETURN_URL_PARAM, $returnUrl);
        }

        return $this;
    }

    /**
     * @param string $returnUrl
     * @return string
     */
    public function createReturnUrlParam($returnUrl)
    {
        return base64_encode($returnUrl);
    }

    /**
     * @return null|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getReturnUrl()
    {
        return $this->getApp()->getGeneralSession()->get(self::RETURN_URL_PARAM);
    }

    /**
     * @return \DaveBaker\Core\Session\General|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function unsetRemoveUrl()
    {
        return $this->getApp()->getGeneralSession()->clear(self::RETURN_URL_PARAM);
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