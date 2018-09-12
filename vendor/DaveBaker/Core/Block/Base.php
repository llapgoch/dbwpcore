<?php

namespace DaveBaker\Core\Block;

abstract class Base extends \DaveBaker\Core\Object\Base
{
    const ANON_SUFFIX = 'child.anon';
    const CAPABILITY_DATA_KEY = 'capabilities';
    const IGNORE_CAPABILITIES_DATA_KEY = 'ignore_capabilities';

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
    /** @var string */
    protected $asName = '';


    /**
     * @var array
     * Allows the output of data array keys without escapeHtml being used
     */
    protected $escapeExcludes = [];

    const ORDER_TYPE_BEFORE = "before";
    const ORDER_TYPE_AFTER = "after";

    public function __construct(
        $name = '',
        $asName = '',
        \DaveBaker\Core\App $app
    ) {
        if(!$name){
            throw new Exception("Block name not set");
        }


        // Store this in the data array so that they can be set via form builders
        $this->setData(self::CAPABILITY_DATA_KEY, []);
        $this->setIgnoreCapabilities(false);

        $this->blockName = $name;
        $this->asName = $asName;

        $this->app = $app;
        $this->childBlocks = $this->getBlockManager()->createBlockList()->setUseAsName(true);


        $this->_construct();
        $this->fireEvent('create');
        $this->init();
    }

    /**
     * @param $url
     * @param array $params
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrl($url, $params = [], $returnUrl = null)
    {
        return $this->getApp()->getHelper('Url')->getUrl($url, $params, $returnUrl);
    }

    /**
     * @param $val
     * @return $this
     */
    public function setIgnoreCapabilities($val)
    {
        $this->setData(self::IGNORE_CAPABILITIES_DATA_KEY, (bool) $val);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getIgnoreCapabilities()
    {
        return $this->getData(self::IGNORE_CAPABILITIES_DATA_KEY);
    }

    /**
     * @param $capability
     * @return $this
     */
    public function addCapability($capability)
    {
        $currentCaps = $this->getCapabilities();

        if(!is_array($this->getData($capability))){
            $capability = [$capability];
        }

        foreach($capability as $cap) {
            if(!in_array($cap, $currentCaps)){
                $currentCaps[] = $cap;
            }
        }

        $this->setData(self::CAPABILITY_DATA_KEY, $currentCaps);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getCapabilities()
    {
        return $this->getData(self::CAPABILITY_DATA_KEY);
    }

    /**
     * @param $pageIdentifier
     * @param array $params
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPageUrl($pageIdentifier, $params = [], $returnUrl = null)
    {
        return $this->getApp()->getHelper('Url')->getPageUrl($pageIdentifier, $params, $returnUrl);
    }

    /**
     * @param $blocks
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function addChildBlock($blocks)
    {
        if(!is_array($blocks)){
            $blocks = [$blocks];
        }
        foreach($blocks as $block) {
            if(!$block instanceof \DaveBaker\Core\Block\BlockInterface){
                throw new Exception("Block is not compatible with BlockInterface");
            }
            $this->childBlocks->add($block);
        }
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
     * @param $type
     * @param $blockName
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
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
     * @return string
     */
    public function getAsName()
    {
        return $this->asName;
    }

    /**
     * @param $asName
     * @throws Exception
     *
     * Always set the block asName when the block's created for the parent's registry
     */
    public function setAsName($asName)
    {
       throw new Exception('As name cannot be set after block creation');
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
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
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
        $context = $this->fireEvent('getchildhtml_' . $this->getName(), ['html' => $context->getHtml()]);

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
     * @return mixed
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function render()
    {
        if(!$this->checkAllowed()){
            return;
        }

        $this->preDispatch();
        $this->_preRender();

        $this->rendered = true;
        $context = $this->fireEvent(
            'render',
             ["html" => $this->_render()]
        );

        return $context->getHtml();
    }


    /**
     * @return $this|void
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function preDispatch()
    {
        if(!$this->checkAllowed()){
            return;
        }

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
     * @return $this|void
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function postDispatch()
    {
        if(!$this->checkAllowed()){
            return;
        }

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
     * @param $attr
     * @return string|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function escAttr($attr)
    {
        return $this->getUtilHelper()->escAttr($attr);
    }

    /**
     * @param $text
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _($text)
    {
        return $this->getUtilHelper()->translate($text);
    }

    /**
     * @param string $html
     * @param string $dataKey
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function escapeHtml($html, $dataKey = '')
    {
        if($dataKey && in_array($dataKey, $this->escapeExcludes)){
            return $html;
        }

        return $this->getUtilHelper()->escapeHtml($html);
    }

    /**
     * @param $className
     * @param string $name
     * @return BlockInterface
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createBlock($className, $name = '', $asName = '')
    {
        if(!$name){
            $name = $this->getAnonymousChildBlockName();
        }

        return $this->getBlockManager()->createBlock($className, $name, $asName);
    }

    /**
     * @return BlockInterface
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getMessagesBlock()
    {
        return $this->getApp()->getBlockManager()->getMessagesBlock();
    }

    /**
     * @return string
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getMessageBlockHtml()
    {
        return $this->getApp()->getBlockManager()->getMessagesBlockHtml();
    }

    /**
     * @return Manager|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getBlockManager()
    {
        return $this->getApp()->getBlockManager();
    }

    /**
     * @param array $excludes
     * @return $this
     */
    public function addEscapeExcludes($excludes = [])
    {
        if(!is_array($excludes)){
            $excludes = [$excludes];
        }

        foreach($excludes as $exclude){
            if(!in_array($exclude, $this->escapeExcludes)){
                $this->escapeExcludes[] = $exclude;
            }
        }

        return $this;
    }

    /**
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function checkAllowed()
    {
        if($this->getIgnoreCapabilities()){
            return true;
        }

        if($caps = $this->getCapabilities()) {
            return $this->getUserHelper()->hasCapability($caps);
        }

        return true;
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
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _render()
    {
        return $this->getHtml() . $this->getChildHtml('');
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
     * @return \DaveBaker\Core\Helper\Util|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getUtilHelper()
    {
        if(!$this->utilHelper) {
            $this->utilHelper = $this->getApp()->getHelper('Util');
        }

        return $this->utilHelper;
    }

    /**
     * @return \DaveBaker\Core\Helper\OutputProcessor\Custom
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getCustomOutputProcessor()
    {
        return $this->getApp()->getHelper('OutputProcessor\Custom');
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