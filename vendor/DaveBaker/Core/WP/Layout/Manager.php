<?php

namespace DaveBaker\Core\WP\Layout;

class Manager extends \DaveBaker\Core\WP\Base
{
    const TEMPLATE_BASE_DIR =  "templates";

    protected $blocks = [];
    protected $shortcodeTags = [];
    protected $namespaceSuffix = "layout_";
    protected $templatePaths = [];
    /** @var \DaveBaker\Core\WP\Config\ConfigInterface */
    protected $config;
    protected $handles = ['default'];

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
     * @param Base $layout
     * @throws Exception
     */
    public function registerLayout(
        \DaveBaker\Core\WP\Layout\Base $layout
    ) {
        $layout->setManager($this);

        /** @var \DaveBaker\Core\Helper\Util $util */
        $util = $this->getApp()->getHelper('Util');

        foreach(get_class_methods($layout) as $method){
            if(preg_match("/Action$/", $method)){
                $handleTag = $util->camelToUnderscore($method);
                $handleTag = preg_replace("/_action$/", "", $handleTag);

                if(!in_array($handleTag, $this->handles)){
                    continue;
                }

                if($blocks = $layout->{$method}()) {

                    if (!is_array($blocks)) {
                        $blocks = [$blocks];
                    }

                    /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
                    foreach ($blocks as $block) {
                        if(!$block->getShortcode()){
                            throw new Exception("Shortcode not set for layout block {$block->getName()}");
                        }

                        if(!isset($this->blocks[$block->getShortcode()])){
                            $this->blocks[$block->getShortcode()] = [];
                        }
                        $this->blocks[$block->getShortcode()][$block->getName()] = $block;
                    }
                }
            }
        }
    }

    /**
     * @param $directory
     * @return $this
     * @throws Exception
     */
    public function addTemplateDirectory($directory)
    {
        if(!file_exists($directory)){
            throw new Exception("Tempate directory '{$directory}' not found.");
        }
        $this->templateDirectories = $directory;

        return $this;
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
        foreach($this->blocks as $tag => $tagBlocks){
            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
            foreach($tagBlocks as $block){
                $block->postDispatch();
            }
        }

        return $this;
    }

    /**
     * @param array $layouts
     */
    public function registerLayouts($layouts = [])
    {
        /** @var \DaveBaker\Core\WP\Layout\Base $layout */
        foreach($layouts as $layout){
            $this->registerLayout($layout);
        }
    }

    /**
     * @return $this
     */
    public function registerShortcodes()
    {
        $shortCodes = array_keys($this->blocks);

        foreach($shortCodes as $shortCode) {
            /** @var  \DaveBaker\Core\WP\Block\BlockInterface $block */
            foreach ($this->getBlocksForShortcode($shortCode) as $block) {
                $block->preDispatch();

                add_shortcode($shortCode, function ($args) use ($shortCode) {
                    $html = "";

                    /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
                    foreach ($this->getBlocksForShortcode($shortCode) as $block) {
                        $html .= $block->render();
                    }

                    return $html;
                });
            }
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
     * @return $this
     * @throws \DaveBaker\WP\Event\Exception
     */
    public function registerHandles()
    {
        if($handles = $this->getEventManager()->fire('register_handles')){
            array_merge($this->handles, $handles);
        }

        // Add page handle
        $post = $this->getApp()->getPageManager()->getCurrentPost();

        // TODO: add in special pages like homepage here
        if ($post) {
            $pageSuffix = str_replace("-", "_", $post->post_name);
            $this->handles[] = $pageSuffix;
        }

        if(is_home()){
            $this->handles[] = "index";
        }

        return $this;
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
     * @param $shortcode
     * @return \DaveBaker\Core\WP\Block\BlockList
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    protected function getBlocksForShortcode($shortcode){
        /** @var \DaveBaker\Core\WP\Block\BlockList $blockList */
        $blockList = $this->getApp()->getBlockManager()->getBlockList();

        if(isset($this->blocks[$shortcode])){
            $blockList->add($this->blocks[$shortcode]);
        }

        if(count($blockList)) {
            $blockList->order();
        }

        return $blockList;
    }
}