<?php

namespace DaveBaker\Form\Block;

class Textarea
    extends Base
    implements ValueSetterInterface
{
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/textarea.phtml');
    }
}