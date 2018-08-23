<?php

namespace DaveBaker\Form\Block\Input;

class Color extends Input
{
    public function init()
    {
        parent::init();
        $this->setInputType('color');
    }
}