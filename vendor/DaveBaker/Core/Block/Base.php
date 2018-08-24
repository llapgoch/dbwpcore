<?php

namespace DaveBaker\Core\Block;

abstract class Base extends \DaveBaker\Core\Object\Base
{
    const ANON_SUFFIX = 'child.anon';

    protected $namespaceCode = 'block';
    /** @var string  */
    protected $blockName;
    /** @var string  */
    protected $orderType = '';
    /** @var   */
    protected $orderBlock;
    /** @var  \DaveBaker\Core\Block\BlockList */
    protected $childBlocks;
    /** @var \DaveBaker\Core\App  */
    protected $app;

    /** @var array  */
    protected $excludedChildren = [];

    // Shortcodes and actions are only used when registering blocks with the layout manager.
    /** @var string  */
    protected $shortcode = '';
    /** @var string  */
    protected $action = '';
    /** @var array  */
    protected $actionArguments = [];
    /** @var bool  */
    protected $isPreDispatched = false;
    /** @var bool  */
    protected $isPostDispatched = false;
    /** @var bool  */
    protected $rendered = false;
    /** @var  \DaveBaker\Core\Helper\Util */
    protected $utilHelper;
    /** @var int */
    protected $anonymousCounter = 1;

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
        $this->childBlocks = $this->app->getBlockManager()->createBlockList();

        $this->fireEvent('create');
        $this->init();
    }

    /**
     * @param BlockInterface $block
     * @return $this
     */
    public function addChildBlock(
        \DaveBaker\Core\Block\BlockInterface $block
    ) {
        $this->childBlocks->add($block);
        return $this;
    }

    /**
     * @param $childBlock string
     * @return $this
     */
    public function excludeChild($childBlock)
    {
        if(!in_array($childBlock, $this->excludedChildren)) {
            $this->excludedChildren[] = $childBlock;
        }

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
     * @param $shortcode
     * @return $this
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
     * @param $type string
     * @param $blockName string
     * @return $this
     * @throws Exception
     */
    public function setOrder($type, $blockName)
    {
        if(!in_array($type, [self::ORDER_TYPE_AFTER, self::ORDER_TYPE_BEFORE])){
            throw new Exception("Invalid order set");
        }

        $this->orderBlock = (string) $blockName;
        $this->orderType = (string) $type;

        $this->fireEvent('order', ['type' => $type, 'blockName' => $blockName]);

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
     * @param string $event
     * @return array
     */
    public function getNamespacedEvent($event)
    {
        $blockName = str_replace(".", "_", $this->getName());
        $eventName = parent::getNamespacedEvent($event);

        return [$eventName, $eventName . "_" . $blockName];
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
     * @param array $exclude
     * @return string
     */
    public function getChildHtml($blockName = '', $exclude = [])
    {

        if($blockName){
            if($block = $this->childBlocks->get($blockName)){
                return $block->render();
            }

            return '';
        }

        if(!is_array($exclude)){
            $exclude = [$exclude];
        }

        $exclude = array_unique(array_merge($this->excludedChildren, $exclude));

        $blockList = clone $this->getChildBlocks();

        if($exclude){
            $blockList->remove($exclude);
        }

        $blockList->order();


        $html = '';

        /** @var \DaveBaker\Core\Block\BlockInterface $block */
        foreach($blockList as $block){
            $html .= $block->render();
        }
        
        $context = $this->fireEvent('getchildhtml', ['html' => $html]);
        return $context->getHtml();
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->_preRender();

        $this->rendered = true;
        $context = $this->fireEvent(
            'render',
             ["html" => $this->getHtml() . $this->getChildHtml('')]
        );

        return $context->getHtml();
    }

    /**
     * @return $this
     */
    protected function init()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public final function preDispatch()
    {
        if($this->isPreDispatched){
           return;
        }

        $this->fireEvent('predispatch_before');

        $this->_preDispatch();

        /** @var \DaveBaker\Core\Block\BlockInterface $child */
        foreach($this->getChildBlocks() as $child){
            $child->preDispatch();
        }

        $this->isPreDispatched = true;
        $this->fireEvent('predispatch_after');

        return $this;
    }

    /**
     * @return bool
     */
    public function isRendered()
    {
        return $this->rendered;
    }

    /**
     * @return $this
     */
    public final function postDispatch()
    {
        if($this->isPostDispatched){
            return;
        }

        $this->fireEvent('predispatch_before');

        $this->_postDispatch();

        /** @var \DaveBaker\Core\Block\BlockInterface $child */
        foreach($this->getChildBlocks() as $child){
            $child->postDispatch();
        }

        $this->isPostDispatched = true;
        $this->fireEvent('predispatch_after');

        return $this;
    }

    /**
     * @param $attr string
     * @return string|void
     */
    public function escAttr($attr)
    {
        return $this->getUtilHelper()->escAttr($attr);
    }

    /**
     * @param $text
     * @return string
     */
    public function _($text)
    {
        return $this->getUtilHelper()->translate($text);
    }

    /**
     * @param $html
     * @return string
     */
    public function escapeHtml($html)
    {
        return $this->getUtilHelper()->escapeHtml($html);
    }

    /**
     * @param $className
     * @param $name
     * @return BlockInterface
     * @throws \DaveBaker\Core\App\Exception
     */
    public function createBlock($className, $name = '')
    {
        if(!$name){
            $name = $this->getAnonymousChildBlockName();
        }

        return $this->getApp()->getBlockManager()->createBlock($className, $name);
    }

    /**
     * @return $this
     */
    protected function _preRender()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _preDispatch()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _postDispatch()
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

    /**
     * @return \DaveBaker\Core\Helper\Util
     */
    protected function getUtilHelper()
    {
        if(!$this->utilHelper) {
            $this->utilHelper = $this->getApp()->getHelper('Util');
        }

        return $this->utilHelper;
    }

    /**
     * @return string
     */
    protected function getAnonymousChildBlockName()
    {
        $name = $this->getName() . "." . self::ANON_SUFFIX . $this->anonymousCounter;
        $this->anonymousCounter++;

        return $name;
    }

}