<?php

namespace DaveBaker\Form\Block;

/**
 * Class Textarea
 * @package DaveBaker\Form\Block
 */
class Textarea
    extends Base
    implements ValueSetterInterface
{
    /**
     * @return \DaveBaker\Core\Block\Template
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->setElementType('textarea');
        return parent::_construct();
    }

    /**
     * @return Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/textarea.phtml');
    }
}