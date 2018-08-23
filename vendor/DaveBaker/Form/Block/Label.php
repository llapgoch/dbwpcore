<?php

namespace DaveBaker\Form\Block;

class Label extends Base
{
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/label.phtml');
    }
}