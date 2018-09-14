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
    protected $addInputTag = false;
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('file');
        parent::_construct();
    }
}