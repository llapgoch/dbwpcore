<?php

namespace DaveBaker\Form\Block\Input;

class Dave
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('date');
    }
}