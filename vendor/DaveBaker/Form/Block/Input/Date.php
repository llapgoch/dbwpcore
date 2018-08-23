<?php

namespace DaveBaker\Form\Block\Input;

class Dave extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('date');
    }
}