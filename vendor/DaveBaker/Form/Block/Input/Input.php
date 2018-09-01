<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Input
 * @package DaveBaker\Form\Block\Input
 */
class Input
    extends \DaveBaker\Form\Block\Base
{
    /** @var string  */
    public $inputValue = '';

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/input.phtml');
        $this->setElementType('text');
        $this->addTagIdentifier('input');
    }

    /**
     * @param $elementType string
     * @return $this
     */
    public function setElementType($elementType)
    {
        $this->removeTagIdentifier("input-" . $this->elementType);
        $this->elementType = $elementType;
        $this->addTagIdentifier('input-' . $elementType);
        return $this;
    }



}