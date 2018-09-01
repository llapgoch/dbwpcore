<?php

namespace DaveBaker\Form\Block;

/**
 * Class Group
 * @package DaveBaker\Form\Block
 */
class Group extends Base
{
    /** @var \DaveBaker\Core\Block\BlockInterface */
    protected $label;
    /** @var \DaveBaker\Core\Block\BlockInterface */
    protected $element;

    public function init()
    {
        $this->setTemplate('form/group.phtml');
        $this->addTagIdentifier('form-group');
        return parent::init();
    }


}