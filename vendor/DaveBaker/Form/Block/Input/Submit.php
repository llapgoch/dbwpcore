<?php

namespace DaveBaker\Form\Block\Input;

class Submit extends Input
{
    public function init()
    {
        parent::init();
        $this->setInputType('submit');
    }
}