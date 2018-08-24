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
    public function init()
    {
        parent::init();
        $this->setElementType('submit');
    }
}