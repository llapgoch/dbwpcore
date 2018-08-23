<?php

namespace DaveBaker\Form\Block\Input;

class Password extends Input
{
    public function init()
    {
        parent::init();
        $this->setInputType('password');
    }
}