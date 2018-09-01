<?php

namespace DaveBaker\Form\Block;

/**
 * Class Form
 * @package DaveBaker\Form\Block
 */
class Form extends Base
{
    protected $formMethod = 'post';
    protected $formAction = '';

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/form.phtml');
        $this->addTagIdentifier('form');
    }

    /**
     * @param $method
     * @return $this
     */
    public function setFormMethod($method)
    {
        $this->formMethod = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormMethod()
    {
        return $this->formMethod;
    }

    public function setFormAction($action)
    {
        $this->formAction = $action;
        return $this;
    }

    public function getFormAction()
    {
        return $this->formAction;
    }
}