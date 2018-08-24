<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Reset
 * @package DaveBaker\Form\Block\Input
 */
class Reset extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('reset');
    }
}