<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Hidden
 * @package DaveBaker\Form\Block\Input
 */
class Hidden
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('hidden');
    }
}