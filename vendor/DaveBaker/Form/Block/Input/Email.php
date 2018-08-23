<?php

namespace DaveBaker\Form\Block\Input;

class Email extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('email');
    }
}