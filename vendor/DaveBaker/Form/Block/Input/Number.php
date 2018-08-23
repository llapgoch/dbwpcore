<?php

namespace DaveBaker\Form\Block\Input;

class Number extends Input
{
    public function init()
    {
        parent::init();
        $this->setInputType('number');
    }
}