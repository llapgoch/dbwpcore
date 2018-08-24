<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Submit
 * @package DaveBaker\Form\Block\Input
 */
class Submit extends Input
{
    public function init()
    {
        parent::init();
        $this->setElementType('submit');
    }
}