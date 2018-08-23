<?php

namespace DaveBaker\Form\Block\Input;

class Input extends \DaveBaker\Form\Block\Base
{
    /** @var string  */
    public $inputValue = '';

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/input.phtml');
        $this->setElementType('text');
    }
}