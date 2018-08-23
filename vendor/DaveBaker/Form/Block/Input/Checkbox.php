<?php

namespace DaveBaker\Form\Block\Input;

class Checkbox extends Input
{
    public function init()
    {
        parent::init();
        $this->setInputType('checkbox');
    }
}