<?php

namespace DaveBaker\Form\Block;

/**
 * Class Form
 * @package DaveBaker\Form\Block
 */
class Form extends Base
{
    protected $method = 'post';
    protected $action = '';

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/form.phtml');
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }
}