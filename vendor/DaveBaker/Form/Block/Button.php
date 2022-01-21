<?php

namespace DaveBaker\Form\Block;

/**
 * Class Button
 * @package DaveBaker\Form\Block
 */
class Button extends Base
{
    /**
     * @return \DaveBaker\Core\Block\Template
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier('button');
        return parent::_construct();
    }

    /**
     * @return Base|void
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('form/button.phtml');
    }

    /**
     * @return Base
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $this->addClass($this->getDefaultClassesForElement());
        $this->addAttribute($this->getDefaultAttributesForElement());

        return parent::_preDispatch();
    }

}