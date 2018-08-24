<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Password
 * @package DaveBaker\Form\Block\Input
 */
class Password
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('password');
    }
}