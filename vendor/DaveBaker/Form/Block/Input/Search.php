<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Search
 * @package DaveBaker\Form\Block\Input
 */
class Search
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('search');
        parent::_construct();
    }
}