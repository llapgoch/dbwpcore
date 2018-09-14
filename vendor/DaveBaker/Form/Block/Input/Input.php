<?php

namespace DaveBaker\Form\Block\Input;

/**
 * Class Input
 * @package DaveBaker\Form\Block\Input
 */
abstract class Input
    extends \DaveBaker\Form\Block\Base
{
    /** @var string  */
    protected $inputValue = '';
    /** @var bool  */
    protected $addInputTag = true;

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        if($this->addInputTag) {
            $this->addTagIdentifier('input');
        }

        parent::_construct();
    }

    /**
     * @return \DaveBaker\Form\Block\Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/input.phtml');
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