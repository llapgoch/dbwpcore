<?php

namespace DaveBaker\Form\Block\Input;

class Color
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('color');
    }
}