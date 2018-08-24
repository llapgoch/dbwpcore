<?php

namespace DaveBaker\Form\Block;

/**
 * Class Base
 * @package DaveBaker\Form\Block
 */
class Base
    extends \DaveBaker\Core\Block\Template
    implements BaseInterface
{
    /** @var string  */
    protected $elementName = '';
    /** @var string  */
    protected $elementValue = '';
    /** @var string  */
    protected $elementType = '';
    
    /**
     * @param $elementName string
     * @return $this
     */
    public function setElementName($elementName)
    {
        $this->elementName = $elementName;
        return $this;
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }

    /**
     * @param $elementType string
     * @return $this
     */
    public function setElementType($elementType)
    {
        $this->elementType = $elementType;
        return $this;
    }

    /**
     * @return string
     */
    public function getElementType()
    {
        return $this->elementType;
    }

    /**
     * @param $elementValue string
     * @return $this
     */
    public function setElementValue($elementValue)
    {
        $this->elementValue = $elementValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getElementValue()
    {
        return $this->elementValue;
    }

}

