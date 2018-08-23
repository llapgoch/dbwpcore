<?php

namespace DaveBaker\Form\Block\Input;

class File extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('file');
    }
}