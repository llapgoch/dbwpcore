<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Email
 * @package DaveBaker\Form\Block\Input
 */
class Email
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('email');
        parent::_construct();
    }
}