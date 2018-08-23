<?php

namespace DaveBaker\Form\Block\Input;

class Text extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('text');
    }
}