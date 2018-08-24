<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Number
 * @package DaveBaker\Form\Block\Input
 */
class Number
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('number');
    }
}