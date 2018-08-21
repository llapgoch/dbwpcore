<?php

namespace DaveBaker\Core\WP\Block;

abstract class Base extends \DaveBaker\Core\Object\Base
{
    protected $blockName;
    protected $orderType = '';
    protected $orderBlock;
    /** @var  \DaveBaker\Core\WP\Block\BlockList */
    protected $childBlocks;
    protected $app;

    // Shortcodes and actions are only used when registering blocks with the layout manager.
    protected $shortcode = '';
    protected $action = '';
    protected $actionArguments = [];

    const ORDER_TYPE_BEFORE = "before";
    const ORDER_TYPE_AFTER = "after";

    public function __construct(
        $name = '',
        \DaveBaker\Core\App $app
    ) {
        if(!$name){
            throw new Exception("Block name not set");
        }

        $this->blockName = $name;
        $this->app = $app;
        $this->childBlocks = $this->app->getBlockManager()->getBlockList();
    }

    public function addChildBlock(
        \DaveBaker\Core\WP\Block\BlockInterface $block
    ) {
        $this->childBlocks->add($block);
        return $this;
    }

    /**
     * @return BlockList
     */
    public function getChildBlocks()
    {
        return $this->childBlocks;
    }

    /**
     * @return boolean
     */
    public function setShortcode($shortcode)
    {
        $this->shortcode = $shortcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortcode()
    {
        return $this->shortcode;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param $type
     * @param $blockName
     * @return $this
     * @throws Exception
     */
    public function setOrder($type = self::ORDER_TYPES_BEFORE, $blockName)
    {
        if(!in_array($type, [self::ORDER_TYPE_AFTER, self::ORDER_TYPE_BEFORE])){
            throw new Exception("Invalid order set");
        }

        $this->orderBlock = (string) $blockName;
        $this->orderType = (string) $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderType()
    {
        return $this->orderType;
    }

    /**
     * @return string
     */
    public function getOrderBlock()
    {
        return $this->orderBlock;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->blockName;
    }

    /**
     * @param $args
     */
    public function setActionArguments($args)
    {
        $this->actionArguments = $args;
    }

    /**
     * @return array
     */
    public function getActionArguments()
    {
        return $this->actionArguments;
    }

    /**
     * @param string $blockName
     * @return string
     */
    public function getChildHtml($blockName = '')
    {
        $this->getChildBlocks()->order();
        
        if($blockName){
            if($block = $this->childBlocks->get($blockName)){
                return $block->render();
            }

            return '';
        }

        $html = '';
        foreach($this->getChildBlocks() as $block){
            $html .= $block->render();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->getHtml() . $this->getChildHtml('');
    }


    public function init()
    {
        return $this;
    }
    /**
     * @return $this
     */
    public function preDispatch()
    {
        var_dump('predispatch ' . $this->getName());
        /** @var \DaveBaker\Core\WP\Block\BlockInterface $child */
        foreach($this->getChildBlocks() as $child){
            $child->preDispatch();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function postDispatch()
    {
        return $this;
    }

    /**
     * @return string
     *
     * Override this method for a baseBlock's content
     */
    protected function getHtml()
    {
        return '';
    }
}