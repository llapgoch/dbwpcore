<?php

namespace DaveBaker\Form\Block;

/**
 * Class Button
 * @package DaveBaker\Form\Block
 */
class Button extends Base
{
    public function init()
    {
        parent::init();
        $this->setElementType('button');
    }
}