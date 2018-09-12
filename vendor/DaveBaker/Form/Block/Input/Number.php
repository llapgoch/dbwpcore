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
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('number');
        parent::_construct();
    }
}