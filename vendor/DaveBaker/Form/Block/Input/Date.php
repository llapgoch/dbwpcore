<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Dave
 * @package DaveBaker\Form\Block\Input
 */
class Date
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('date');
        parent::_construct();
    }
}