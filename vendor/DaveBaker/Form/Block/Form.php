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

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/form.phtml');
        $this->addTagIdentifier('form');
    }

    /**
     * @param \DaveBaker\Core\Block\BlockList $blockList
     * @return array
     */
    protected function getFormElements(
        \DaveBaker\Core\Block\BlockList $blockList
    ) {
        $valueBlocks = [];

        foreach($blockList as $block){
            if($block instanceof \DaveBaker\Form\Block\ValueSetterInterface){
                $valueBlocks[] = $block;
            }

            $valueBlocks = array_merge($valueBlocks, $this->getFormElements(($block->getChildBlocks())));
        }

        return $valueBlocks;
    }

    /**
     * @param bool $useCache
     * @return array
     */
    public function getValueFormElements($useCache = true)
    {
        if(!$this->valueFormElementsCache || $useCache == false){
           $this->valueFormElementsCache = $this->getFormElements($this->getChildBlocks());
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