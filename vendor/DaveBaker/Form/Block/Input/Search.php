<?php

namespace DaveBaker\Form\Block\Input;

class Search extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('search');
    }
}