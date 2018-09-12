<?php

namespace DaveBaker\Form\Block;

/**
 * Class Label
 * @package DaveBaker\Form\Block
 */
class Label extends Base
{

    /**
     * @return \DaveBaker\Core\Block\Template
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->setElementType('label');
        return parent::_construct();
    }

    /**
     * @return Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/label.phtml');
    }

    /**
     * @param $forId
     * @return $this
     */
    public function setForId($forId)
    {
        $this->setData('for_id', $forId);
        return $this;
    }

    /**
     * @return string
     */
    public function getForId()
    {
        return $this->getData('for_id');
    }

    /**
     * @return string
     */
    public function getLabelName()
    {
        return $this->getData('label_name');
    }

    /**
     * @param $labelName
     * @return $this
     */
    public function setLabelName($labelName)
    {
        $this->setData('label_name', $labelName);
        return $this;
    }
}