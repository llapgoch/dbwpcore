<?php

namespace DaveBaker\Form\Block\Input;

class Reset extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('reset');
    }
}