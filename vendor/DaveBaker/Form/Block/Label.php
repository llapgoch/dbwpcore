<?php

namespace DaveBaker\Form\Block;

/**
 * Class Label
 * @package DaveBaker\Form\Block
 */
class Label extends Base
{
    /** @var string  */
    protected $forId = '';
    /** @var string */
    protected $labelName = '';

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/label.phtml');
        $this->setElementType('label');
    }

    /**
     * @param $forId
     * @return $this
     */
    public function setForId($forId)
    {
        $this->forId = $forId;
        return $this;
    }

    /**
     * @return string
     */
    public function getForId()
    {
        return $this->forId;
    }

    /**
     * @return string
     */
    public function getLabelName()
    {
        return $this->labelName;
    }

    /**
     * @param $labelName
     * @return $this
     */
    public function setLabelName($labelName)
    {
        $this->labelName = (string) $labelName;
        return $this;
    }
}