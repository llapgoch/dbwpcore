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
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('password');
        parent::_construct();
    }
}