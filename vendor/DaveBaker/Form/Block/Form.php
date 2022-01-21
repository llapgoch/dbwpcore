<?php

namespace DaveBaker\Form\Block;

/**
 * Class Form
 * @package DaveBaker\Form\Block
 */
class Form extends Base
{
    /** @var string  */
    protected $formMethod = 'post';
    /** @var string  */
    protected $formAction = '';
    /** @var array  */
    protected $valueFormElementsCache = [];
    /** @var bool  */
    protected $isLocked = false;

    public function _construct()
    {
        $this->addTagIdentifier('form');
        parent::_construct();
    }

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/form.phtml');
    }

    /**
     * @param \DaveBaker\Core\Block\BlockList $blockList
     * @return array
     */
    protected function getFormElementsFromBlockList(
        \DaveBaker\Core\Block\BlockList $blockList
    ) {
        $valueBlocks = [];

        foreach($blockList as $block){
            if($block instanceof ValueSetterInterface){
                $valueBlocks[] = $block;
            }

            $valueBlocks = array_merge($valueBlocks, $this->getFormElementsFromBlockList(($block->getChildBlocks())));
        }

        return $valueBlocks;
    }

    /**
     * @return $this
     */
    public function lock()
    {
        $this->setIsLocked(true);
        /** @var ValueSetterInterface $element */
        foreach($this->getValueFormElements() as $element){
            if($element->getIgnoreLock() == false) {
                $element->setLock(true);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function unlock()
    {
        $this->setIsLocked(false);
        /** @var ValueSetterInterface $element */
        foreach($this->getValueFormElements() as $element){
            if($element->getIgnoreLock() == false) {
                $element->setLock(false);
            }
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    protected function setIsLocked($value)
    {
        $this->setData(self::LOCKED_DATA_KEY, (bool) $value);
        return $this;
    }

    /**
     * @param bool $useCache
     * @return array
     */
    public function getValueFormElements($useCache = true)
    {
        if(!$this->valueFormElementsCache || $useCache == false){
           $this->valueFormElementsCache = $this->getFormElementsFromBlockList($this->getChildBlocks());
        }

        return $this->valueFormElementsCache;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setFormMethod($method)
    {
        $this->formMethod = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormMethod()
    {
        return $this->formMethod;
    }

    public function setFormAction($action)
    {
        $this->formAction = $action;
        return $this;
    }

    public function getFormAction()
    {
        if($this->formAction) {
            return $this->formAction;
        }

        return $this->getUrlHelper()->getCurrentUrl();
    }
}