<?php

namespace DaveBaker\Form\Block\Input;

class Hidden extends Input
{
    public function init()
    {
        parent::init();
        $this->setInputType('hidden');
    }
}