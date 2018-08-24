<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Radio
 * @package DaveBaker\Form\Block\Input
 */
class Radio
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('radio');
    }
}