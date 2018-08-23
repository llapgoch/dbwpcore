<?php

namespace DaveBaker\Form\Block;

class Label extends Base
{
    /** @var string  */
    protected $forId = '';

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
}