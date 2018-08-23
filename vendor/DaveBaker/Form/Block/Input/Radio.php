<?php

namespace DaveBaker\Form\Block\Input;

class Radio extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('radio');
    }
}