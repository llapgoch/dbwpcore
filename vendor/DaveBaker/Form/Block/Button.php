<?php

namespace DaveBaker\Form\Block;

/**
 * Class Button
 * @package DaveBaker\Form\Block
 */
class Button extends Base
{
    public function init()
    {
        parent::init();
        $this->setTemplate('form/button.phtml');
        $this->addTagIdentifier('button');
    }

    protected function _preDispatch()
    {
        $this->addClass($this->getDefaultClassesForElement());
        $this->addAttribute($this->getDefaultAttributesForElement());

        return parent::_preDispatch();
    }

    public function getAttrs($includeClass = true)
    {
        $attrString = $this->makeAttrs($this->getAttributes());

        if($includeClass && $this->classes){
            $attrString .= $this->makeClassString($this->getClasses());
        }

        return $attrString;
    }
}