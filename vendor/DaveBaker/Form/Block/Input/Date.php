<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Dave
 * @package DaveBaker\Form\Block\Input
 */
class Date
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('date');
    }
}