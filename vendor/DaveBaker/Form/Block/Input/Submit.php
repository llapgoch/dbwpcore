<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Submit
 * @package DaveBaker\Form\Block\Input
 *
 * Submit does not implement ValueSetterInterface as the element by default should not accept a user submitted values
 */
class Submit extends Input
{
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('submit');
        parent::_construct();
    }
}