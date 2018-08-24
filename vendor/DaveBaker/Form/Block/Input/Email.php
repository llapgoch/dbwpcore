<?php

namespace DaveBaker\Form\Block\Input;

class Email
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('email');
    }
}