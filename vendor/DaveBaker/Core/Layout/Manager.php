<?php

namespace DaveBaker\Core\Layout;

class Manager extends \DaveBaker\Core\Base
{
    const TEMPLATE_BASE_DIR =  "templates";
    const DEFAULT_ACTION_ARGUMENTS = 100;

    /** @var string */
    protected $namespaceCode = "layout";
    
    /** @var array */
    protected $shortcodeBlocks = [];
    
    /** @var array */
    protected $actionBlocks = [];
    
    /** @var array  */
    protected $templatePaths = [];

    /*  Because registering the layouts may run several times (because not all WP actions run on
        every page, so multiple are registered, we keep track of what's already been executed here
    */
    /** @var array  */
    protected $registeredLayouts = [];
    /** @var array  */
    protected $registeredHandles = [];
    
    /** @var \DaveBaker\Core\Config\ConfigInterface */
    protected $config;

    /** @var bool */
    protected $isDispatched = false;

    public function __construct(
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\Config\ConfigInterface $config
    ) {
        parent::__construct($app);
        $this->config = $config;
        
        $this->registerTemplatePaths();
    }
    
    /**
     * @return $this
     */
    public final function preDispatch()
    {
        $this->fireEvent('predispatch_before');
        $actionCodes = array_keys($this->actionBlocks);
        $shortCodes = array_keys($this->shortcodeBlocks);

        foreach($actionCodes as $actionCode){
           $this->preDispatchBlocks($this->getBlocksForAction($actionCode));
        }

        foreach($shortCodes as $shortCode){
            $this->preDispatchBlocks($this->getBlocksForShortcode($shortCode));
        }

        $this->_preDispatch();
        $this->fireEvent('predispatch_after');

        return $this;
    }

    /**
     * @return $this
     */
    public final function postDispatch()
    {
        $this->fireEvent('postdispatch_before');
        $actionCodes = array_keys($this->actionBlocks);
        $shortCodes = array_keys($this->shortcodeBlocks);

        foreach($actionCodes as $actionCode){
            $this->postDispatchBlocks($this->getBlocksForAction($actionCode));
        }

        foreach($shortCodes as $shortCode){
            $this->postDispatchBlocks($this->getBlocksForShortcode($shortCode));
        }

        $this->_postDispatch();
        $this->fireEvent('postdispatch_after');

        return $this;
    }

    /**
     * @param $layouts string|array
     * @throws Exception
     */
    public function register($layouts)
    {
        if(!is_array($layouts)){
            $layouts = [$layouts];
        }

        /** @var string $layout */
        foreach($layouts as $layout){

            try{
                /** @var \DaveBaker\Core\Layout\Base $layoutInstance */
                if(isset($this->registeredLayouts[$layout])){
                    $layoutInstance = $this->registeredLayouts[$layout];
                }else {
                    $layoutInstance = $this->getApp()->getObjectManager()->getAppObject($layout);
                }

                $this->registeredLayouts[$layout] = $layoutInstance;

                if(!$layoutInstance instanceof \DaveBaker\Core\Layout\Base){
                    throw new Exception('Layout is of incorrect type');
                }

                $this->registerLayout($layoutInstance);
            } catch (\Exception $e){
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * @return $this
     */
    public function registerShortcodes()
    {
        $shortCodes = array_keys($this->shortcodeBlocks);

        foreach($shortCodes as $shortCode) {
            /** @var  \DaveBaker\Core\Block\BlockInterface $block */

            add_shortcode($shortCode, function ($args) use ($shortCode) {
                $html = "";

                /** @var \DaveBaker\Core\Block\BlockInterface $block */
                foreach ($this->getBlocksForShortcode($shortCode) as $block) {

                    // Set shortcode data on the block
                    if($args) {
                        foreach ($args as $argKey => $arg) {
                            $block->setData($argKey, $arg);
                        }
                    }

                    $html .= $block->render();
                }

                return $html;
            });
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function registerActions()
    {
        $actionCodes = array_keys($this->actionBlocks);

        /** @var string $actionCode */
        foreach($actionCodes  as $actionCode) {
            /** @var  \DaveBaker\Core\Block\BlockInterface $block */

                add_action($actionCode, function () use ($actionCode) {
                    $html = "";

                    $args = [];
                    foreach(func_get_args() as $funcArg){
                        if($funcArg !== ""){
                            $args[] = $funcArg;
                        }
                    }

                    /** @var \DaveBaker\Core\Block\BlockInterface $block */
                    foreach ($this->getBlocksForAction($actionCode) as $block) {
                        // Set action arguments on the block, this will be an indexed array
                        $block->setActionArguments($args);
                        $html .= $block->render();
                    }

                    echo $html;
                }, 10, self::DEFAULT_ACTION_ARGUMENTS);
            }


        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentThemeDirectory()
    {
        return get_stylesheet_directory();
    }

    /**
     * @return array
     */
    public function getTemplatePaths()
    {
        return $this->templatePaths;
    }

    /**
     * @param $blocks array
     * @return $this
     */
    protected function preDispatchBlocks($blocks)
    {
        $this->fireEvent('predispatch_blocks_before');

        /** @var  \DaveBaker\Core\Block\BlockInterface $block */
        foreach($blocks as $block){
            $block->preDispatch();
        }

        $this->fireEvent('predispatch_blocks_after');

        return $this;
    }

    /**
     * @param $blocks array
     * @return $this
     */
    protected function postDispatchBlocks($blocks)
    {
        $this->fireEvent('postdispatch_blocks_before');

        /** @var  \DaveBaker\Core\Block\BlockInterface $block */
        foreach($blocks as $block){
            $block->postDispatch();
        }

        $this->fireEvent('postdispatch_blocks_after');
        return $this;
    }

    /**
     * @param Base $layout
     * @throws Exception
     */
    protected function registerLayout(
        \DaveBaker\Core\Layout\Base $layout
    ) {
        /** @var \DaveBaker\Core\Helper\Util $util */

        $util = $this->getApp()->getHelper('Util');

        foreach(get_class_methods($layout) as $method) {
            if (preg_match("/Handle/", $method)) {
                $handleTag = $util->camelToUnderscore($method);
                $handleTag = preg_replace("/_handle$/", "", $handleTag);

                if (!in_array($handleTag, $this->getApp()->getHandleManager()->getHandles())) {
                    continue;
                }

                if(!isset($this->registeredHandles[$handleTag])){
                    $this->registeredHandles[$handleTag] = [];
                }

                if(in_array($layout, $this->registeredHandles[$handleTag])){
                    continue;
                }

                $this->registeredHandles[$handleTag][] = $layout;

                // Run each of the action methods for registered handles, creating the blocks
                $layout->{$method}();
            }
        }

        // Get the blocks from the layout
        if($blocks = $layout->getBlocks()) {

            if (!is_array($blocks)) {
                $blocks = [$blocks];
            }

            /** @var \DaveBaker\Core\Block\BlockInterface $block */
            foreach ($blocks as $block) {
                if(!$block->getShortcode() && !$block->getAction()){
                    throw new Exception("Shortcode or action not set for layout block {$block->getName()}");
                }

                if($block->getShortcode()) {
                    $this->registerBlockForShortcode($block->getShortcode(), $block);
                }

                if($block->getAction()){
                    $this->registerBlockForAction($block->getAction(), $block);
                }
            }
        }

    }

    /**
     * @return $this
     */
    protected function _postDispatch()
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
     * @param $shortcode string
     * @param \DaveBaker\Core\Block\BlockInterface $block
     */
    protected function registerBlockForShortcode(
        $shortcode,
        \DaveBaker\Core\Block\BlockInterface $block
    ) {
        if (!isset($this->shortcodeBlocks[$block->getShortcode()])) {
            $this->shortcodeBlocks[$shortcode] = [];
        }
        $this->shortcodeBlocks[$shortcode][$block->getName()] = $block;
    }

    /**
     * @param $action
     * @param \DaveBaker\Core\Block\BlockInterface $block
     */
    protected function registerBlockForAction(
        $action,
        \DaveBaker\Core\Block\BlockInterface $block
    ) {
        if (!isset($this->actionBlocks[$action])) {
            $this->actionBlocks[$action] = [];
        }
        $this->actionBlocks[$action][$block->getName()] = $block;
    }

    /**
     * @return $this
     */
    protected function registerTemplatePaths()
    {
        if(is_array($this->config->getConfigValue('templates'))){
            $templates = array_reverse($this->config->getConfigValue('templates'));

            foreach($templates as $template){
                $fullPath = WP_CONTENT_DIR . DS . "plugins" . DS . $template;

                if(file_exists($fullPath)){
                    $this->templatePaths[] = $fullPath;
                }
            }
        }

        // Try the theme directory first
        $themeLocation = $this->getCurrentThemeDirectory() . DS . self::TEMPLATE_BASE_DIR;

        if(file_exists($themeLocation)){
            array_unshift($this->templatePaths, $themeLocation);
        }

        $context = $this->fireEvent('register_template_paths', ['template_paths' => $this->templatePaths]);
        $this->templatePaths = $context->getTemplatePaths();

        return $this;
    }

    /**
     * @param $shortcode string
     * @return \DaveBaker\Core\Block\BlockList
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getBlocksForShortcode($shortcode){
        /** @var \DaveBaker\Core\Block\BlockList $blockList */
        $blockList = $this->getApp()->getBlockManager()->createBlockList();

        if(isset($this->shortcodeBlocks[$shortcode])){
            $blockList->add($this->shortcodeBlocks[$shortcode]);
        }

        if(count($blockList)) {
            $blockList->order();
        }

        return $blockList;
    }

    /**
     * @param $action string
     * @return \DaveBaker\Core\Block\BlockList
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getBlocksForAction($action){
        /** @var \DaveBaker\Core\Block\BlockList $blockList */
        $blockList = $this->getApp()->getBlockManager()->createBlockList();

        if(isset($this->actionBlocks[$action])){
            $blockList->add($this->actionBlocks[$action]);
        }

        if(count($blockList)) {
            $blockList->order();
        }

        return $blockList;
    }
}