<?php

namespace DaveBaker\Form\Block;

/**
 * Class Button
 * @package DaveBaker\Form\Block
 */
class Button extends Base
{
    protected $mainTagName = 'button';

    public function init()
    {
        parent::init();
        $this->setElementType('button');
    }
}