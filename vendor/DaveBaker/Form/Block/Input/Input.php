<?php

namespace DaveBaker\Form\Block\Input;

class Input extends \DaveBaker\Form\Block\Base
{
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/input.phtml');
        $this->setInputType('text');
    }
}