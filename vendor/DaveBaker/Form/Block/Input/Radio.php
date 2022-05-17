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
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('radio');
        parent::_construct();
    }
}