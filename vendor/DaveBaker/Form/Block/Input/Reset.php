<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Reset
 * @package DaveBaker\Form\Block\Input
 */
class Reset extends Input
{
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('reset');
        parent::_construct();
    }
}