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


    /**
     * @return \DaveBaker\Core\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
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

        return $this;
    }

    /**
     * @return bool
     */
    public function isRest()
    {
        return ( defined( 'REST_REQUEST' ) && REST_REQUEST );
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * @param string $returnUrl
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createReturnUrlParam($returnUrl = '')
    {
        return base64_encode($returnUrl !== '' ? $returnUrl : $this->getUrlHelper()->getCurrentUrl());
    }

    /**
     * @return null|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getReturnUrl()
    {
        if($this->getRequest()->getParam(self::RETURN_URL_PARAM)) {
            return base64_decode($this->getRequest()->getParam(self::RETURN_URL_PARAM));
        }

        return null;
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