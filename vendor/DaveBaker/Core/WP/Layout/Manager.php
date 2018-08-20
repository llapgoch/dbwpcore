<?php

namespace DaveBaker\Core\WP\Layout;

class Manager extends \DaveBaker\Core\WP\Base
{
    protected $blocks = [];
    protected $shortcodeTags = [];
    protected $namespaceSuffix = "page_";

    /**
     * @param \DaveBaker\Core\WP\Layout\Base $layout
     */
    public function registerLayout(
        \DaveBaker\Core\WP\Layout\Base $layout
    ) {
        $layout->setManager($this);

        /** @var \DaveBaker\Core\Helper\Util $util */
        $util = $this->getApp()->getHelper('Util');

        foreach(get_class_methods($layout) as $method){
            if(preg_match("/Action$/", $method)){
                $tag = $util->camelToUnderscore($method);

                $tag = preg_replace("/_action$/", "", $tag);
                if($blocks = $layout->{$method}()) {

                    if (!isset($this->blocks[$tag])) {
                        $this->blocks[$tag] = [];
                    }

                    if (!is_array($blocks)) {
                        $blocks = [$blocks];
                    }

                    /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
                    foreach ($blocks as $block) {
                        $this->blocks[$tag][$block->getName()] = $block;
                    }

                    add_shortcode($tag, function () {});
                }
            }
        }
    }

    public function preDispatch()
    {

    }

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

    public function registerShortcodes()
    {
        global $shortcode_tags;

        if($shortcode_tags) {
            $this->shortcodeTags = $shortcode_tags;
        }

        $dispatched = [];

        /*
        Render Blocks here -----------------------
        */
        foreach($this->shortcodeTags as $k => $tag){

            /** @var  \DaveBaker\Core\WP\Block\BlockInterface $block */
            foreach($this->getBlocksForShortcode($k) as $block){
                if(!in_array($block->getName(), $dispatched)){
                    $block->preDispatch();
                    $dispatched[] = $block->getName();
                }
            }

            add_shortcode($k, function() use ($k){
                $html = "";

                /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
                foreach($this->getBlocksForShortcode($k) as $block) {
                    $html .= $block->render();
                }

                return $html;
            });
        };
        
    }

    /**
     * @param $shortcode
     * @return \DaveBaker\Core\WP\Block\BlockList
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    protected function getBlocksForShortcode($shortcode){
        /** @var \DaveBaker\Core\WP\Block\BlockList $blockList */
        $blockList = $this->getApp()->getBlockManager()->getBlockList();
        $post = $post = $this->getApp()->getPageManager()->getCurrentPost();

        if(isset($this->blocks[$shortcode])){
            $blockList->add($this->blocks[$shortcode]);
        }

        if ($post) {
            $pageSuffix = str_replace("-", "_", $post->post_name);

            if (isset($this->blocks[$shortcode . "_" . $pageSuffix])) {
                $blockList->add($this->blocks[$shortcode . "_" . $pageSuffix]);
            }
        }

        if(count($blockList)) {
            $blockList->order();
        }

        return $blockList;
    }
}