<?php

namespace DaveBaker\Form\Block;

class Form extends Base
{
    protected function init()
    {
        $this->setTemplate('form/form.phtml');
    }
}