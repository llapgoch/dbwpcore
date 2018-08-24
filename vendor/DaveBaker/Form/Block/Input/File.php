<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class File
 * @package DaveBaker\Form\Block\Input
 */
class File
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    public function init()
    {
        parent::init();
        $this->setElementType('file');
    }
}