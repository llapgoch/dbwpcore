<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Checkbox
 * @package DaveBaker\Form\Block\Input
 */
class Checkbox
    extends Input
    implements \DaveBaker\Form\Block\ValueSetterInterface
{

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('checkbox');
        parent::_construct();
    }


}