<?php

namespace DaveBaker\Form\Block;

class Textarea extends Base
{
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/textarea.phtml');
    }
}