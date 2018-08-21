<?php

namespace DaveBaker\Core\WP\Layout;

class Manager extends \DaveBaker\Core\WP\Base
{
    const TEMPLATE_BASE_DIR =  "templates";
    const DEFAULT_ACTION_ARGUMENTS = 100;

    protected $shortcodeBlocks = [];
    protected $actionBlocks = [];
    protected $namespaceSuffix = "layout_";
    protected $templatePaths = [];
    /** @var \DaveBaker\Core\WP\Config\ConfigInterface */
    protected $config;

    public function __construct(
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\WP\Option\Manager $optionManager = null,
        \DaveBaker\Core\WP\Config\ConfigInterface $config
    ) {
        parent::__construct($app, $optionManager);
        $this->config = $config;
        
        $this->registerTemplatePaths();
    }
    
    /**
     * @return $this
     */
    public function preDispatch()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function postDispatch()
    {
        /**
         * @var  $tag string
         * @var  $tagBlocks array
         */
        /** TODO: Only created blocks should fire a postDispatch */
//        foreach($this->blocks as $tag => $tagBlocks){
//            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
//            foreach($tagBlocks as $block){
//                $block->postDispatch();
//            }
//        }

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
                /** @var \DaveBaker\Core\WP\Layout\Base $layoutInstance */
                $layoutInstance = $this->getApp()->getObjectManager()->get($layout, [$this->getApp()]);

                if(!$layoutInstance instanceof \DaveBaker\Core\WP\Layout\Base){
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
            /** @var  \DaveBaker\Core\WP\Block\BlockInterface $block */
            foreach ($this->getBlocksForShortcode($shortCode) as $block) {
                $block->preDispatch();

                add_shortcode($shortCode, function ($args) use ($shortCode) {
                    $html = "";

                    /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
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
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function registerActions()
    {
        $actionCodes = array_keys($this->actionBlocks);

        foreach($actionCodes as $actionCode){
            $blocks = $this->getBlocksForAction($actionCode);
            /** @var  \DaveBaker\Core\WP\Block\BlockInterface $block */
            foreach($blocks as $block){
                $block->preDispatch();
            }
        }

        /** @var string $actionCode */
        foreach($actionCodes  as $actionCode) {
            /** @var  \DaveBaker\Core\WP\Block\BlockInterface $block */

                add_action($actionCode, function () use ($actionCode) {
                    $html = "";

                    $args = [];
                    foreach(func_get_args() as $funcArg){
                        if($funcArg !== ""){
                            $args[] = $funcArg;
                        }
                    }

                    /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
                    foreach ($this->getBlocksForAction($actionCode) as $block) {
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
     * @param Base $layout
     * @throws Exception
     */
    protected function registerLayout(
        \DaveBaker\Core\WP\Layout\Base $layout
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

                // Run each of the action methods for registered handles, creating the blocks
                $layout->{$method}();
            }
        }

        // Get the blocks from the layout
        if($blocks = $layout->getBlocks()) {

            if (!is_array($blocks)) {
                $blocks = [$blocks];
            }

            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
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
     * @param $shortcode string
     * @param \DaveBaker\Core\WP\Block\BlockInterface $block
     */
    protected function registerBlockForShortcode(
        $shortcode,
        \DaveBaker\Core\WP\Block\BlockInterface $block
    ) {
        if (!isset($this->shortcodeBlocks[$block->getShortcode()])) {
            $this->shortcodeBlocks[$shortcode] = [];
        }
        $this->shortcodeBlocks[$shortcode][$block->getName()] = $block;
    }

    /**
     * @param $action
     * @param \DaveBaker\Core\WP\Block\BlockInterface $block
     */
    protected function registerBlockForAction(
        $action,
        \DaveBaker\Core\WP\Block\BlockInterface $block
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

        return $this;
    }

    /**
     * @param $shortcode string
     * @return \DaveBaker\Core\WP\Block\BlockList
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    protected function getBlocksForShortcode($shortcode){
        /** @var \DaveBaker\Core\WP\Block\BlockList $blockList */
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
     * @return \DaveBaker\Core\WP\Block\BlockList
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    protected function getBlocksForAction($action){
        /** @var \DaveBaker\Core\WP\Block\BlockList $blockList */
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