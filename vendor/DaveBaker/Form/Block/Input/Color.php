<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Color
 * @package DaveBaker\Form\Block\Input
 */
class Color
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    protected $mainTagName = 'input-checkbox';

    public function init()
    {
        parent::init();
        $this->setElementType('color');
    }
}