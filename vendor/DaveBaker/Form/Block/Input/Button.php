<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Checkbox
 * @package DaveBaker\Form\Block\Input
 */
class Checkbox extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('button');
    }
}