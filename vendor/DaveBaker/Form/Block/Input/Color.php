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
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('color');
        parent::_construct();
    }
}