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
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('hidden');
        parent::_construct();
    }
}